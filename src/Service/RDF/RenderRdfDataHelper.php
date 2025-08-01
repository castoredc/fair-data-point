<?php
declare(strict_types=1);

namespace App\Service\RDF;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Data\InstanceData;
use App\Entity\Castor\Data\RecordData;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;
use App\Entity\Castor\Structure\Report;
use App\Entity\Castor\Structure\Survey;
use App\Entity\Data\DistributionContents\Dependency\DependencyGroup as DistributionContentsDependencyGroup;
use App\Entity\Data\DistributionContents\Dependency\DependencyRule as DistributionContentsDependencyRule;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\Common\Dependency\DependencyGroup as DataSpecificationDependencyGroup;
use App\Entity\DataSpecification\Common\Dependency\DependencyRule as DataSpecificationDependencyRule;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Enum\CastorEntityType;
use App\Entity\FAIRData\Distribution;
use App\Entity\Terminology\Annotation;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use App\Service\CastorEntityHelper;
use App\Service\DataTransformationService;
use App\Service\UriHelper;
use EasyRdf\Graph;
use EasyRdf\Literal;
use function assert;
use function count;
use function in_array;

class RenderRdfDataHelper extends RdfRenderHelper
{
    private CastorStudy $study;
    private RDFDistribution $contents;
    private CastorEntityCollection $optionGroups;

    public function __construct(
        Distribution $distribution,
        private ApiClient $apiClient,
        private CastorEntityHelper $entityHelper,
        private UriHelper $uriHelper,
        private DataTransformationService $dataTransformationService,
        ?CastorStudy $study,
        ?CastorEntityCollection $optionGroups,
    ) {
        $contents = $distribution->getContents();
        assert($contents instanceof RDFDistribution);

        $this->contents = $contents;

        $dbStudy = $distribution->getDataset()->getStudy();

        $this->study = $study ?? $this->apiClient->getStudy($dbStudy->getSourceId());

        assert($dbStudy instanceof CastorStudy);

        $this->optionGroups = $optionGroups ?? $this->entityHelper->getEntitiesByType($dbStudy, CastorEntityType::fieldOptionGroup());
    }

    private function renderModule(RecordData $data, Graph $graph, DataModelGroup $module): void
    {
        if ($module->isDependent()) {
            $shouldRender = $this->parseDependencies($module->getDependencies(), $data);
        } else {
            $shouldRender = true;
        }

        if (! $shouldRender) {
            return;
        }

        $triples = $module->getElementGroups();

        foreach ($triples as $triple) {
            assert($triple instanceof Triple);

            $subject = $graph->resource($this->getURI($data, $triple->getSubject()));
            $predicate = $triple->getPredicate()->getIri()->getValue();
            $object = $triple->getObject();

            $isLiteral = ($object instanceof LiteralNode || ($object instanceof ValueNode && ! $object->isAnnotatedValue()));
            $value = $this->getValue($data, $object);

            if ($value === null) {
                continue;
            }

            if ($isLiteral) {
                $literal = new Literal($value, null, 'xsd:' . $object->getDataType()->toString());
                $graph->addLiteral($subject, $predicate, $literal);
            } else {
                $graph->add($subject, $predicate, $graph->resource($value));
            }
        }
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function renderRecord(Record $record, Graph $graph): Graph
    {
        $dataModel = $this->contents->getCurrentDataModelVersion();
        $modules = $dataModel->getGroups();

        foreach ($modules as $module) {
            assert($module instanceof DataModelGroup);
            if ($module->isRepeated()) {
                $mapping = $this->contents->getMappingByGroupForCurrentVersion($module);

                if ($mapping === null) {
                    continue;
                }

                $entity = $mapping->getEntity();

                if ($entity instanceof Report) {
                    $data = $record->getData()->getReport();
                } elseif ($entity instanceof Survey) {
                    $data = $record->getData()->getSurvey();
                } else {
                    continue;
                }

                $instances = $data->getInstances();

                foreach ($instances as $instance) {
                    /** @var Instance $instance */
                    $this->renderModule($data->getInstanceData($instance), $graph, $module);
                }
            } else {
                $data = $record->getData()->getStudy();
                $this->renderModule($data, $graph, $module);
            }
        }

        return $graph;
    }

    /**
     * @param Record[] $records
     *
     * @return Record[]
     */
    public function getSubset(array $records): array
    {
        $return = [];

        foreach ($records as $record) {
            $newRecord = $record->hasData() ? $record : $this->apiClient->getRecordDataCollection($this->study, $record);

            if ($this->contents->getDependencies() === null) {
                $return[] = $newRecord;
            } elseif (! $this->parseSubsetDependencies($this->contents->getDependencies(), $newRecord->getData()->getStudy())) {
                continue;
            } else {
                $return[] = $newRecord;
            }
        }

        return $return;
    }

    private function getURI(RecordData $data, Node $node): string
    {
        $uri = '';
        $record = $data->getRecord();

        if ($node instanceof RecordNode) {
            $uri = $this->uriHelper->getUri($this->contents) . '/' . $record->getId();
        } elseif ($node instanceof InternalIriNode) {
            $uri = $this->uriHelper->getUri($this->contents) . '/' . $record->getId() . '/' . $node->getSlug();

            if ($node->isRepeated() && $data instanceof InstanceData) {
                $uri .= '/' . $data->getInstance()->getId();
            }
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }

    private function getValue(RecordData $data, Node $node): ?string
    {
        if ($node instanceof LiteralNode) {
            if ($node->isPlaceholder()) {
                $placeholderType = $node->getPlaceholderType();
                $record = $data->getRecord();

                if ($placeholderType->isRecordId()) {
                    return $record->getId();
                }

                if ($placeholderType->isInstituteId()) {
                    return $record->getInstitute()->getId();
                }

                if ($placeholderType->isInstituteAbbreviation()) {
                    return $record->getInstitute()->getAbbreviation();
                }

                if ($placeholderType->isInstituteCode()) {
                    return $record->getInstitute()->getCode();
                }

                if ($placeholderType->isInstituteName()) {
                    return $record->getInstitute()->getName();
                }

                if ($placeholderType->isInstituteCountryCode()) {
                    return $record->getInstitute()->getCountry()->getCode();
                }

                if ($placeholderType->isInstituteCountryName()) {
                    return $record->getInstitute()->getCountry()->getName();
                }
            }

            return $node->getValue();
        }

        if (! ($node instanceof ValueNode)) {
            return $this->getURI($data, $node);
        }

        $mapping = $this->contents->getMappingByElementForCurrentVersion($node);

        if ($mapping === null) {
            return null;
        }

        if ($mapping->shouldTransformData()) {
            $variables = [];

            foreach ($mapping->getEntities() as $entity) {
                $fieldResult = $data->getFieldResultByFieldId($entity->getId());

                if ($fieldResult === null) {
                    return null;
                }

                $variables[$entity->getSlug()] = $fieldResult->getValue();
            }

            return $this->dataTransformationService->render($mapping->getSyntax(), $variables);
        }

        $entity = $mapping->getEntities()[0];
        $fieldResult = $data->getFieldResultByFieldId($entity->getId());

        if ($fieldResult !== null) {
            if ($node->isAnnotatedValue()) {
                // Annotated value
                $optionGroup = $this->optionGroups->getById($fieldResult->getField()->getOptionGroupId());

                if ($optionGroup === null) {
                    return null;
                }

                assert($optionGroup instanceof FieldOptionGroup);
                $option = $optionGroup->getOptionByValue($fieldResult->getValue());

                if ($option === null) {
                    return null;
                }

                $annotations = $option->getAnnotations();

                if (count($annotations) === 0) {
                    return null;
                }

                $annotation = $annotations->first();
                assert($annotation instanceof Annotation);

                return $annotation->getConcept()->getUrl()->getValue();
            }

            // 'Plain' value
            return $this->transformValue($node->getDataType(), $fieldResult->getValue());
        }

        return null;
    }

    protected function parseDependencies(DataSpecificationDependencyGroup $group, RecordData $data): bool
    {
        $outcomes = [];
        $combinator = $group->getCombinator();

        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DataSpecificationDependencyGroup) {
                $outcomes[] = $this->parseDependencies($rule, $data);
            } elseif ($rule instanceof DataSpecificationDependencyRule) {
                $node = $rule->getElement();
                assert($node instanceof ValueNode);
                $operator = $rule->getOperator();
                $compareValue = $this->transformValue($node->getDataType(), $rule->getValue());

                $value = $this->getValue($data, $node);

                if ($value === null && $data instanceof InstanceData) {
                    $value = $this->getValue($data->getRecord()->getData()->getStudy(), $node);
                }

                $outcomes[] = $this->compareValue($operator, $node->getDataType(), $value, $compareValue);
            }
        }

        if ($combinator->isAnd()) {
            return ! in_array(false, $outcomes, true);
        }

        if ($combinator->isOr()) {
            return in_array(true, $outcomes, true);
        }

        return false;
    }

    protected function parseSubsetDependencies(DistributionContentsDependencyGroup $group, RecordData $data): bool
    {
        $outcomes = [];
        $combinator = $group->getCombinator();

        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DistributionContentsDependencyGroup) {
                $outcomes[] = $this->parseSubsetDependencies($rule, $data);
            } elseif ($rule instanceof DistributionContentsDependencyRule) {
                if ($rule->getType()->isInstitute()) {
                    $outcomes[] = $this->parseInstituteDependency($rule, $data);
                } elseif ($rule->getType()->isValueNode()) {
                    $outcomes[] = $this->parseValueNodeDependency($rule, $data);
                }
            }
        }

        if ($combinator->isAnd()) {
            return ! in_array(false, $outcomes, true);
        }

        if ($combinator->isOr()) {
            return in_array(true, $outcomes, true);
        }

        return false;
    }

    protected function parseInstituteDependency(DistributionContentsDependencyRule $rule, RecordData $data): bool
    {
        $institute = $data->getRecord()->getInstitute()->getId();

        if ($rule->getOperator()->isEqual()) {
            return $institute === $rule->getValue();
        }

        if ($rule->getOperator()->isNotEqual()) {
            return $institute !== $rule->getValue();
        }

        return false;
    }

    protected function parseValueNodeDependency(DistributionContentsDependencyRule $rule, RecordData $data): bool
    {
        $node = $rule->getNode();
        $compareValue = $this->transformValue($node->getDataType(), $rule->getValue());

        $value = $this->getValue($data, $node);

        if ($value === null && $data instanceof InstanceData) {
            $value = $this->getValue($data->getRecord()->getData()->getStudy(), $node);
        }

        return $this->compareValue($rule->getOperator(), $node->getDataType(), $value, $compareValue);
    }
}

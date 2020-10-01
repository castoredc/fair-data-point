<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Data\InstanceData;
use App\Entity\Castor\Data\RecordData;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;
use App\Entity\Castor\Structure\Report;
use App\Entity\Castor\Structure\Survey;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyRule;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Triple;
use App\Entity\Data\DistributionContentsDependency\DistributionContentsDependencyGroup;
use App\Entity\Data\DistributionContentsDependency\DistributionContentsDependencyRule;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\Enum\CastorEntityType;
use App\Entity\Enum\DependencyOperatorType;
use App\Entity\Enum\XsdDataType;
use App\Entity\FAIRData\Distribution;
use App\Entity\Terminology\Annotation;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use DateTimeImmutable;
use EasyRdf_Graph;
use EasyRdf_Literal;
use function assert;
use function boolval;
use function count;
use function floatval;
use function in_array;

class RDFRenderHelper
{
    private ApiClient $apiClient;
    private CastorEntityHelper $entityHelper;
    private CastorStudy $study;
    private RDFDistribution $contents;
    private UriHelper $uriHelper;
    private CastorEntityCollection $optionGroups;

    public function __construct(Distribution $distribution, ApiClient $apiClient, CastorEntityHelper $entityHelper, UriHelper $uriHelper)
    {
        $this->apiClient = $apiClient;
        $this->entityHelper = $entityHelper;
        $this->uriHelper = $uriHelper;

        $contents = $distribution->getContents();
        assert($contents instanceof RDFDistribution);

        $this->contents = $contents;

        $dbStudy = $distribution->getDataset()->getStudy();
        $this->study = $this->apiClient->getStudy($dbStudy->getSourceId());
        assert($dbStudy instanceof CastorStudy);

        $this->optionGroups = $this->entityHelper->getEntitiesByType($dbStudy, CastorEntityType::fieldOptionGroup());
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function renderRecord(Record $record, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $dataModel = $this->contents->getCurrentDataModelVersion();
        $modules = $dataModel->getModules();

        foreach ($modules as $module) {
            if ($module->isRepeated()) {
                $mapping = $this->contents->getMappingByModuleForCurrentVersion($module);

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

    private function renderModule(RecordData $data, EasyRdf_Graph $graph, DataModelModule $module): void
    {
        if ($module->isDependent()) {
            $shouldRender = $this->parseDependencies($module->getDependencies(), $data);
        } else {
            $shouldRender = true;
        }

        if (! $shouldRender) {
            return;
        }

        $triples = $module->getTriples();

        foreach ($triples as $triple) {
            /** @var Triple $triple */
            $subject = $graph->resource($this->getURI($data, $triple->getSubject()));
            $predicate = $triple->getPredicate()->getIri()->getValue();
            $object = $triple->getObject();

            $isLiteral = ($object instanceof LiteralNode || ($object instanceof ValueNode && ! $object->isAnnotatedValue()));
            $value = $this->getValue($data, $object);

            if ($value === null) {
                continue;
            }

            if ($isLiteral) {
                assert($object instanceof LiteralNode || $object instanceof ValueNode);

                $literal = new EasyRdf_Literal($value, null, 'xsd:' . $object->getDataType()->toString());
                $graph->addLiteral($subject, $predicate, $literal);
            } else {
                $graph->add($subject, $predicate, $graph->resource($value));
            }
        }
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

        $mapping = $this->contents->getMappingByNodeForCurrentVersion($node);

        if ($mapping === null) {
            return null;
        }

        $entity = $mapping->getEntity();
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

    private function transformValue(?XsdDataType $dataType, string $value): string
    {
        if ($dataType === null) {
            return $value;
        }

        if ($dataType->isDateTimeType()) {
            $date = new DateTimeImmutable($value);

            if ($dataType->isDateTime()) {
                return $date->format('Y-m-d\TH:i:s');
            }

            if ($dataType->isDate()) {
                return $date->format('Y-m-d');
            }

            if ($dataType->isTime()) {
                return $date->format('H:i:s');
            }

            if ($dataType->isGDay()) {
                return '---' . $date->format('d');
            }

            if ($dataType->isGMonth()) {
                return '--' . $date->format('m');
            }

            if ($dataType->isGYear()) {
                return $date->format('Y');
            }

            if ($dataType->isGYearMonth()) {
                return $date->format('Y-m');
            }

            if ($dataType->isGMonthDay()) {
                return '--' . $date->format('m-d');
            }
        } elseif ($dataType->isNumberType()) {
            return $value;
        } elseif ($dataType->isBooleanType()) {
            return (string) boolval($value);
        }

        return $value;
    }

    private function compareValue(DependencyOperatorType $operator, ?XsdDataType $dataType, ?string $value, string $compareTo): bool
    {
        if ($dataType === null) {
            $dataType = XsdDataType::string();
        }

        if ($value === null) {
            return $operator->isNull();
        }

        if ($dataType->isDateTimeType()) {
            $value = new DateTimeImmutable($value);
            $compareTo = new DateTimeImmutable($compareTo);
        } elseif ($dataType->isNumberType()) {
            $value = floatval($value);
            $compareTo = floatval($compareTo);
        } elseif ($dataType->isBooleanType()) {
            $value = boolval($value);
            $compareTo = boolval($compareTo);
        }

        if ($operator->isNull()) {
            return false;
        }

        if ($operator->isNotNull()) {
            return true;
        }

        if ($operator->isEqual()) {
            return $value === $compareTo;
        }

        if ($operator->isNotEqual()) {
            return $value !== $compareTo;
        }

        if ($operator->isSmallerThan()) {
            return $value < $compareTo;
        }

        if ($operator->isSmallerThanOrEqualTo()) {
            return $value <= $compareTo;
        }

        if ($operator->isGreaterThan()) {
            return $value > $compareTo;
        }

        if ($operator->isGreaterThanOrEqualTo()) {
            return $value >= $compareTo;
        }

        return false;
    }

    private function parseDependencies(DataModelDependencyGroup $group, RecordData $data): bool
    {
        $outcomes = [];
        $combinator = $group->getCombinator();

        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DataModelDependencyGroup) {
                $outcomes[] = $this->parseDependencies($rule, $data);
            } elseif ($rule instanceof DataModelDependencyRule) {
                $node = $rule->getNode();
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

    /**
     * @param Record[] $records
     *
     * @return Record[]
     */
    public function getSubset(array $records): array
    {
        $return = [];

        foreach ($records as $record) {
            $newRecord = $this->apiClient->getRecordDataCollection($this->study, $record);

            if ($this->contents->getDependencies() === null) {
                $return[] = $newRecord;
            }

            if (! $this->parseSubsetDependencies($this->contents->getDependencies(), $newRecord->getData()->getStudy())) {
                continue;
            }

            $return[] = $newRecord;
        }

        return $return;
    }

    private function parseSubsetDependencies(DistributionContentsDependencyGroup $group, RecordData $data): bool
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

    private function parseInstituteDependency(DistributionContentsDependencyRule $rule, RecordData $data): bool
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

    private function parseValueNodeDependency(DistributionContentsDependencyRule $rule, RecordData $data): bool
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

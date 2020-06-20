<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Data\FieldResult;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Castor\Record;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Triple;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\Enum\CastorEntityType;
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

class RDFRenderHelper
{
    /** @var ApiClient */
    private $apiClient;

    /** @var CastorEntityHelper */
    private $entityHelper;

    /** @var CastorStudy */
    private $study;

    /** @var RDFDistribution */
    private $contents;

    /** @var UriHelper */
    private $uriHelper;

    /** @var CastorEntityCollection */
    private $optionGroups;

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
        $record = $this->apiClient->getRecordDataCollection($this->study, $record);

        $dataModel = $this->contents->getDataModel();
        $modules = $dataModel->getModules();

        foreach ($modules as $module) {
            /** @var DataModelModule $module */
            $triples = $module->getTriples();

            foreach ($triples as $triple) {
                /** @var Triple $triple */
                $subject = $graph->resource($this->getURI($record, $triple->getSubject()));
                $predicate = $triple->getPredicate()->getIri()->getValue();
                $object = $triple->getObject();

                $isLiteral = ($object instanceof LiteralNode || ($object instanceof ValueNode && ! $object->isAnnotatedValue()));

                if ($isLiteral) {
                    assert($object instanceof LiteralNode || $object instanceof ValueNode);

                    $values = $this->getValue($record, $object);

                    foreach ($values as $value) {
                        $literal = new EasyRdf_Literal($value, null, 'xsd:' . $object->getDataType()->toString());
                        $graph->addLiteral($subject, $predicate, $literal);
                    }
                } else {
                    $values = $this->getValue($record, $object);

                    foreach ($values as $value) {
                        $graph->add($subject, $predicate, $graph->resource($value));
                    }
                }
            }
        }

        return $graph;
    }

    private function getURI(Record $record, Node $node): string
    {
        $uri = '';

        if ($node instanceof RecordNode) {
            $uri = $this->uriHelper->getUri($this->contents) . '/' . $record->getId();
        } elseif ($node instanceof InternalIriNode) {
            $uri = $this->uriHelper->getUri($this->contents) . '/' . $record->getId() . '/' . $node->getSlug();
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }

    /** @return string[]|null */
    private function getValue(Record $record, Node $node): ?array
    {
        $values = [];

        if ($node instanceof LiteralNode) {
            $values[] = $node->getValue();
        } elseif ($node instanceof ValueNode) {
            $mapping = $this->contents->getMappingByNode($node);

            if ($mapping === null) {
                return null;
            }

            $entity = $mapping->getEntity();
            $fieldResults = $record->getData()->getFieldResultByFieldId($entity->getId());

            if ($fieldResults !== null) {
                foreach ($fieldResults as $fieldResult) {
                    if ($node->isAnnotatedValue()) {
                        // Annotated value
                        $optionGroup = $this->optionGroups->getById($fieldResult->getField()->getOptionGroupId());

                        if ($optionGroup === null) {
                            return null;
                        }

                        assert($optionGroup instanceof FieldOptionGroup);

                        $option = $optionGroup->getOptionByValue($fieldResult->getValue());
                        $annotations = $option->getAnnotations();

                        if (count($annotations) === 0) {
                            return null;
                        }

                        $annotation = $annotations->first();
                        assert($annotation instanceof Annotation);

                        $values[] = $annotation->getConcept()->getUrl()->getValue();
                    } else {
                        // 'Plain' value
                        $values[] = $this->transformValue($node->getDataType(), $fieldResult);
                    }
                }
            }
        } else {
            $values[] = $this->getURI($record, $node);
        }

        return $values;
    }

    private function transformValue(XsdDataType $dataType, FieldResult $fieldResult): string
    {
        $value = $fieldResult->getValue();

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
}

<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Data\FieldResult;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Castor\Record;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\NamespacePrefix;
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
use App\Entity\Terminology\Annotation;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use App\Security\CastorUser;
use App\Service\CastorEntityHelper;
use App\Service\UriHelper;
use DateTimeImmutable;
use EasyRdf_Graph;
use EasyRdf_Literal;
use EasyRdf_Namespace;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;
use function boolval;
use function count;

class RenderRDFDistributionCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    /** @var Security */
    private $security;

    /** @var CastorEntityHelper */
    private $entityHelper;

    /** @var RDFDistribution */
    private $distribution;

    /** @var CastorStudy */
    private $study;

    /** @var CastorEntityCollection */
    private $optionGroups;

    /** @var UriHelper */
    private $uriHelper;

    public function __construct(ApiClient $apiClient, Security $security, CastorEntityHelper $entityHelper, UriHelper $uriHelper)
    {
        $this->apiClient = $apiClient;
        $this->security = $security;
        $this->entityHelper = $entityHelper;
        $this->uriHelper = $uriHelper;
    }

    /**
     * @throws Exception
     */
    public function __invoke(RenderRDFDistributionCommand $command): EasyRdf_Graph
    {
        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        $dbStudy = $command->getDistribution()->getDistribution()->getDataset()->getStudy();
        assert($dbStudy instanceof CastorStudy);

        $this->optionGroups = $this->entityHelper->getEntitiesByType($dbStudy, CastorEntityType::fieldOptionGroup());

        // if ($message->getDistribution()->getAccessRights() === DistributionAccessType::PUBLIC) {
        //     $this->apiClient->useApiUser($message->getCatalog()->getApiUser());
        // } else {
        //     $this->apiClient->setUser($user);
        // }

        $this->apiClient->setUser($user);
        $this->study = $this->apiClient->getStudy($dbStudy->getSourceId());
        $this->distribution = $command->getDistribution();

        $graph = new EasyRdf_Graph();

        $dataModel = $command->getDistribution()->getDataModel();
        $prefixes = $dataModel->getPrefixes();

        foreach ($prefixes as $prefix) {
            /** @var NamespacePrefix $prefix */
            EasyRdf_Namespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        foreach ($command->getRecords() as $record) {
            $graph = $this->renderRecord($record, $graph);
        }

        return $graph;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function renderRecord(Record $record, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $record = $this->apiClient->getRecordDataCollection($this->study, $record);

        $dataModel = $this->distribution->getDataModel();
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

                    $literal = new EasyRdf_Literal($this->getValue($record, $object), null, 'xsd:' . $object->getDataType()->toString());
                    $graph->addLiteral($subject, $predicate, $literal);
                } else {
                    $graph->add($subject, $predicate, $graph->resource($this->getValue($record, $object)));
                }
            }
        }

        return $graph;
    }

    private function getURI(Record $record, Node $node): string
    {
        $uri = '';

        if ($node instanceof RecordNode) {
            $uri = $this->uriHelper->getUri($this->distribution) . '/' . $record->getId();
        } elseif ($node instanceof InternalIriNode) {
            $uri = $this->uriHelper->getUri($this->distribution) . '/' . $record->getId() . '/' . $node->getSlug();
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }

    private function getValue(Record $record, Node $node): ?string
    {
        if ($node instanceof LiteralNode) {
            return $node->getValue();
        }

        if ($node instanceof ValueNode) {
            $mapping = $this->distribution->getMappingByNode($node);

            if ($mapping === null) {
                return null;
            }

            $entity = $mapping->getEntity();
            $fieldResult = $record->getData()->getFieldResultByFieldId($entity->getId());

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

                return $annotation->getConcept()->getUrl()->getValue();
            }

            // 'Plain' value
            return $this->transformValue($node->getDataType(), $fieldResult);
        }

        return $this->getURI($record, $node);
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

<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Api\Resource\Data\DataModel\DataModelModuleRDFPreviewApiResource;
use App\Api\Resource\Data\DataModel\DataModelRDFPreviewApiResource;
use App\Command\Data\DataModel\GetDataModelRDFPreviewCommand;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Exception\InvalidNodeType;
use App\Exception\InvalidValueType;
use App\Exception\NoAccessPermission;
use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\RdfNamespace;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetDataModelRDFPreviewCommandHandler
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     */
    public function __invoke(GetDataModelRDFPreviewCommand $command): DataModelRDFPreviewApiResource
    {
        $dataModelVersion = $command->getDataModelVersion();
        $dataModel = $dataModelVersion->getDataModel();

        if (! $this->security->isGranted('view', $dataModel)) {
            throw new NoAccessPermission();
        }

        $modulePreviews = [];

        $fullGraph = new Graph();

        $modules = $dataModelVersion->getGroups();
        $prefixes = $dataModelVersion->getPrefixes();

        $dataModelVersionTriples = [];

        foreach ($prefixes as $prefix) {
            RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        foreach ($modules as $module) {
            assert($module instanceof DataModelGroup);
            $moduleGraph = new Graph();

            $triples = $module->getTriples();

            foreach ($triples as $triple) {
                assert($triple instanceof Triple);

                $subject = $triple->getSubject();
                $subjectInFullGraph = $fullGraph->resource($this->getURI($subject));
                $subjectInModuleGraph = $moduleGraph->resource($this->getURI($subject));

                $predicate = $triple->getPredicate();
                $predicateUri = $predicate->getIri()->getValue();

                $object = $triple->getObject();

                $isLiteral = ($object instanceof LiteralNode || ($object instanceof ValueNode && ! $object->isAnnotatedValue()));

                if ($isLiteral) {
                    $literal = new Literal($this->getValue($object));
                    $fullGraph->addLiteral($subjectInFullGraph, $predicateUri, $literal);
                    $moduleGraph->addLiteral($subjectInModuleGraph, $predicateUri, $literal);
                } else {
                    $fullGraph->add($subjectInFullGraph, $predicateUri, $fullGraph->resource($this->getValue($object)));
                    $moduleGraph->add($subjectInModuleGraph, $predicateUri, $moduleGraph->resource($this->getValue($object)));
                }

                $dataModelVersionTriples[] = $triple;
            }

            $modulePreviews[] = new DataModelModuleRDFPreviewApiResource($module, $moduleGraph->serialise('turtle'));
        }

        return new DataModelRDFPreviewApiResource($dataModelVersionTriples, $modulePreviews, $fullGraph->serialise('turtle'));
    }

    private function getValue(Node $node): string
    {
        if ($node instanceof LiteralNode) {
            if ($node->isPlaceholder()) {
                $placeholderType = $node->getPlaceholderType();

                if ($placeholderType->isRecordId()) {
                    return '##Record ID##';
                }

                if ($placeholderType->isInstituteId()) {
                    return '##Institute ID##';
                }

                if ($placeholderType->isInstituteName()) {
                    return '##Institute Name##';
                }

                if ($placeholderType->isInstituteAbbreviation()) {
                    return '##Institute Abbreviation##';
                }

                if ($placeholderType->isInstituteCode()) {
                    return '##Institute Code##';
                }

                if ($placeholderType->isInstituteCountryCode()) {
                    return '##Institute Country Code##';
                }

                if ($placeholderType->isInstituteCountryName()) {
                    return '##Institute Country Name##';
                }
            }

            return $node->getValue();
        }

        if ($node instanceof ValueNode) {
            if ($node->isAnnotatedValue()) {
                return '##Annotated value of ' . $node->getTitle() . '##';
            }

            return '##Plain value of ' . $node->getTitle() . ' (' . $node->getDataType() . ')##';
        }

        return $this->getURI($node);
    }

    private function getURI(Node $node): string
    {
        $uri = '';

        if ($node instanceof RecordNode) {
            $uri = '/##record_id##';
        } elseif ($node instanceof InternalIriNode) {
            $uri = '/##record_id##/' . $node->getSlug();

            if ($node->isRepeated()) {
                $uri .= '/##instance_id##';
            }
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }
}

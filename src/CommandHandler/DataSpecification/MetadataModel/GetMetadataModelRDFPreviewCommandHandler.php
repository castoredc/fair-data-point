<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelModuleRDFPreviewApiResource;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelRDFPreviewApiResource;
use App\Command\DataSpecification\MetadataModel\GetMetadataModelRDFPreviewCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\DataSpecification\Common\Model\InvalidValueType;
use App\Exception\NoAccessPermission;
use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\RdfNamespace;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function sprintf;

#[AsMessageHandler]
class GetMetadataModelRDFPreviewCommandHandler
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     */
    public function __invoke(GetMetadataModelRDFPreviewCommand $command): MetadataModelRDFPreviewApiResource
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted('view', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $modulePreviews = [];

        $fullGraph = new Graph();

        $modules = $metadataModelVersion->getGroups();
        $prefixes = $metadataModelVersion->getPrefixes();

        $metadataModelVersionTriples = [];

        foreach ($prefixes as $prefix) {
            RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        foreach ($modules as $module) {
            assert($module instanceof MetadataModelGroup);
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

                $metadataModelVersionTriples[] = $triple;
            }

            $modulePreviews[] = new MetadataModelModuleRDFPreviewApiResource($module, $moduleGraph->serialise('turtle'));
        }

        return new MetadataModelRDFPreviewApiResource($metadataModelVersionTriples, $modulePreviews, $fullGraph->serialise('turtle'));
    }

    private function getValue(Node $node): string
    {
        if ($node instanceof LiteralNode) {
            if ($node->isPlaceholder()) {
                $placeholderType = $node->getPlaceholderType();

                if ($placeholderType->isCreatedAt()) {
                    return '##Created At##';
                }

                if ($placeholderType->isUpdatedAt()) {
                    return '##Updated at##';
                }

                if ($placeholderType->isResourceUrl()) {
                    return '##Resource URL##';
                }

                if ($placeholderType->isMetadataVersion()) {
                    return '##Metadata Version##';
                }

                if ($placeholderType->isDistributionAccessUrl()) {
                    return '##Distribution Access URL##';
                }

                if ($placeholderType->isDistributionMediaType()) {
                    return '##Distribution Media Type##';
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
            $uri = '/##record[' . $node->getResourceType() . ']##';
        } elseif ($node instanceof InternalIriNode) {
            $uri = '/##record##/' . $node->getSlug();
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        } elseif ($node instanceof ChildrenNode) {
            $uri = sprintf('##Children (%s)##', $node->getResourceType()->getLabel());
        } elseif ($node instanceof ParentsNode) {
            $uri = sprintf('##Parents (%s)##', $node->getResourceType()->getLabel());
        }

        return $uri;
    }
}

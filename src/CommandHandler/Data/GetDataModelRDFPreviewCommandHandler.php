<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Api\Resource\Data\DataModel\DataModelModuleRDFPreviewApiResource;
use App\Api\Resource\Data\DataModel\DataModelRDFPreviewApiResource;
use App\Command\Data\DataModel\GetDataModelRDFPreviewCommand;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Triple;
use App\Exception\InvalidNodeType;
use App\Exception\InvalidValueType;
use App\Exception\NoAccessPermission;
use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\RdfNamespace;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class GetDataModelRDFPreviewCommandHandler implements MessageHandlerInterface
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
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $modulePreviews = [];

        $fullGraph = new Graph();

        $dataModel = $command->getDataModel();
        $modules = $dataModel->getModules();
        $prefixes = $dataModel->getPrefixes();

        $dataModelTriples = [];

        foreach ($prefixes as $prefix) {
            RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        foreach ($modules as $module) {
            $moduleGraph = new Graph();

            $triples = $module->getTriples();

            foreach ($triples as $triple) {
                /** @var Triple $triple */
                $subject = $triple->getSubject();
                $subjectInFullGraph = $fullGraph->resource($this->getURI($subject));
                $subjectInModuleGraph = $moduleGraph->resource($this->getURI($subject));

                $predicate = $triple->getPredicate();
                $predicateUri = $predicate->getIri()->getValue();

                $object = $triple->getObject();

                $isLiteral = ($object instanceof LiteralNode || ($object instanceof ValueNode && ! $object->isAnnotatedValue()));

                if ($isLiteral) {
                    assert($object instanceof LiteralNode || $object instanceof ValueNode);

                    $literal = new Literal($this->getValue($object));
                    $fullGraph->addLiteral($subjectInFullGraph, $predicateUri, $literal);
                    $moduleGraph->addLiteral($subjectInModuleGraph, $predicateUri, $literal);
                } else {
                    $fullGraph->add($subjectInFullGraph, $predicateUri, $fullGraph->resource($this->getValue($object)));
                    $moduleGraph->add($subjectInModuleGraph, $predicateUri, $moduleGraph->resource($this->getValue($object)));
                }

                $dataModelTriples[] = $triple;
            }

            $modulePreviews[] = new DataModelModuleRDFPreviewApiResource($module, $moduleGraph->serialise('turtle'));
        }

        return new DataModelRDFPreviewApiResource($dataModelTriples, $modulePreviews, $fullGraph->serialise('turtle'));
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

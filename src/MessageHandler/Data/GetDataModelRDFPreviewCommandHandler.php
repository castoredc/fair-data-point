<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Api\Resource\Data\DataModelModuleRDFPreviewApiResource;
use App\Api\Resource\Data\DataModelRDFPreviewApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Triple;
use App\Exception\InvalidNodeType;
use App\Exception\InvalidValueType;
use App\Message\Data\GetDataModelRDFPreviewCommand;
use EasyRdf_Graph;
use EasyRdf_Literal;
use EasyRdf_Namespace;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function assert;

class GetDataModelRDFPreviewCommandHandler implements MessageHandlerInterface
{
    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     */
    public function __invoke(GetDataModelRDFPreviewCommand $command): DataModelRDFPreviewApiResource
    {
        $modulePreviews = [];

        $fullGraph = new EasyRdf_Graph();

        $dataModel = $command->getDataModel();
        $modules = $dataModel->getModules();
        $prefixes = $dataModel->getPrefixes();

        foreach ($prefixes as $prefix) {
            /** @var NamespacePrefix $prefix */
            EasyRdf_Namespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        foreach ($modules as $module) {
            $moduleGraph = new EasyRdf_Graph();

            /** @var DataModelModule $module */
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

                    $literal = new EasyRdf_Literal($this->getValue($object), null, 'xsd:' . $object->getDataType());
                    $fullGraph->addLiteral($subjectInFullGraph, $predicateUri, $literal);
                    $moduleGraph->addLiteral($subjectInModuleGraph, $predicateUri, $literal);
                } else {
                    $fullGraph->add($subjectInFullGraph, $predicateUri, $fullGraph->resource($this->getValue($object)));
                    $moduleGraph->add($subjectInModuleGraph, $predicateUri, $moduleGraph->resource($this->getValue($object)));
                }
            }

            $modulePreviews[] = new DataModelModuleRDFPreviewApiResource($module, $moduleGraph->serialise('turtle'));
        }

        return new DataModelRDFPreviewApiResource($modulePreviews, $fullGraph->serialise('turtle'));
    }

    private function getValue(Node $node): string
    {
        if ($node instanceof LiteralNode) {
            return $node->getValue();
        }

        if ($node instanceof ValueNode) {
            if ($node->isAnnotatedValue()) {
                return '##Annotated value of ' . $node->getTitle() . '##';
            }

            return '##Plain value of ' . $node->getTitle() . '##';
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
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }
}

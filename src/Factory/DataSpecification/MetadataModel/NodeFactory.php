<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\XsdDataType;
use App\Entity\Iri;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;

class NodeFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(MetadataModelVersion $version, array $data): Node
    {
        $type = NodeType::fromString($data['type']);

        $title = $data['title'];
        $description = $data['description'];

        if ($type->isRecord()) {
            $newNode = new RecordNode($version, ResourceType::fromString($data['value']['resourceType']));
        } elseif ($type->isInternalIri()) {
            $newNode = new InternalIriNode($version, $title, $description);
            $newNode->setSlug($data['value']);
            $newNode->setIsRepeated($data['repeated']);
        } elseif ($type->isExternalIri()) {
            $newNode = new ExternalIriNode($version, $title, $description);
            $newNode->setIri(new Iri($data['value']['value']));
        } elseif ($type->isLiteral()) {
            $newNode = new LiteralNode($version, $title, $description);
            $newNode->setValue($data['value']['value']);
            $newNode->setDataType(XsdDataType::fromString($data['value']['dataType']));
        } elseif ($type->isValue()) {
            $newNode = new ValueNode($version, $title, $description);
            $isAnnotated = $data['value']['value'] === 'annotated';

            $newNode->setIsAnnotatedValue($isAnnotated);

            if (! $isAnnotated) {
                $newNode->setDataType(XsdDataType::fromString($data['value']['dataType']));
            }
        } elseif ($type->isChildren()) {
            $newNode = new ChildrenNode($version, ResourceType::fromString($data['value']['resourceType']));
        } elseif ($type->isParents()) {
            $newNode = new ParentsNode($version, ResourceType::fromString($data['value']['resourceType']));
        } else {
            throw new InvalidNodeType();
        }

        return $newNode;
    }
}

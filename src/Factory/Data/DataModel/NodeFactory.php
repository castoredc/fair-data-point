<?php
declare(strict_types=1);

namespace App\Factory\Data\DataModel;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use App\Entity\Iri;
use App\Exception\InvalidNodeType;

class NodeFactory
{
    /** @param array<mixed> $data */
    public function createFromJson(DataModelVersion $version, array $data): Node
    {
        $type = NodeType::fromString($data['type']);

        $title = $data['title'];
        $description = $data['description'];

        if ($type->isRecord()) {
            $newNode = new RecordNode($version);
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
            $newNode->setIsRepeated($data['repeated']);

            $newNode->setIsAnnotatedValue($isAnnotated);

            if (! $isAnnotated) {
                $newNode->setDataType(XsdDataType::fromString($data['value']['dataType']));
            }
        } else {
            throw new InvalidNodeType();
        }

        return $newNode;
    }
}

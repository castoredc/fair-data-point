<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode as DataModelExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode as DataModelInternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode as DataModelLiteralNode;
use App\Entity\DataSpecification\DataModel\Node\RecordNode as DataModelRecordNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode as DataModelValueNode;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode as MetadataModelChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode as MetadataModelExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode as MetadataModelInternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode as MetadataModelLiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode as MetadataModelParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode as MetadataModelRecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode as MetadataModelValueNode;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;

/**
 * @method static static externalIri()
 * @method static static internalIri()
 * @method static static literal()
 * @method static static record()
 * @method static static value()
 * @method static static children()
 * @method static static parents()
 * @method bool isExternalIri()
 * @method bool isInternalIri()
 * @method bool isLiteral()
 * @method bool isRecord()
 * @method bool isValue()
 * @method bool isChildren()
 * @method bool isParents()
 * @inheritDoc
 */
class NodeType extends Enum
{
    private const EXTERNAL_IRI = 'external';
    private const INTERNAL_IRI = 'internal';
    private const LITERAL = 'literal';
    private const RECORD = 'record';
    private const VALUE = 'value';
    private const CHILDREN = 'children';
    private const PARENTS = 'parents';

    /** @return class-string<object> */
    public function getClassNameForDataModel()
    {
        if ($this->isExternalIri()) {
            return DataModelExternalIriNode::class;
        }

        if ($this->isInternalIri()) {
            return DataModelInternalIriNode::class;
        }

        if ($this->isLiteral()) {
            return DataModelLiteralNode::class;
        }

        if ($this->isRecord()) {
            return DataModelRecordNode::class;
        }

        if ($this->isValue()) {
            return DataModelValueNode::class;
        }

        throw new InvalidNodeType();
    }

    /** @return class-string<object> */
    public function getClassNameForMetadataModel()
    {
        if ($this->isExternalIri()) {
            return MetadataModelExternalIriNode::class;
        }

        if ($this->isInternalIri()) {
            return MetadataModelInternalIriNode::class;
        }

        if ($this->isLiteral()) {
            return MetadataModelLiteralNode::class;
        }

        if ($this->isRecord()) {
            return MetadataModelRecordNode::class;
        }

        if ($this->isValue()) {
            return MetadataModelValueNode::class;
        }

        if ($this->isChildren()) {
            return MetadataModelChildrenNode::class;
        }

        if ($this->isParents()) {
            return MetadataModelParentsNode::class;
        }

        throw new InvalidNodeType();
    }
}

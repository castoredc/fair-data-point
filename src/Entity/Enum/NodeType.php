<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Exception\InvalidNodeType;

/**
 * @method static static externalIri()
 * @method static static internalIri()
 * @method static static literal()
 * @method static static record()
 * @method static static value()
 * @method bool isExternalIri()
 * @method bool isInternalIri()
 * @method bool isLiteral()
 * @method bool isRecord()
 * @method bool isValue()
 * @inheritDoc
 */
class NodeType extends Enum
{
    private const EXTERNAL_IRI = 'external';
    private const INTERNAL_IRI = 'internal';
    private const LITERAL = 'literal';
    private const RECORD = 'record';
    private const VALUE = 'value';

    /** @return class-string<object> */
    public function getClassName()
    {
        if ($this->isExternalIri()) {
            return ExternalIriNode::class;
        }

        if ($this->isInternalIri()) {
            return InternalIriNode::class;
        }

        if ($this->isLiteral()) {
            return LiteralNode::class;
        }

        if ($this->isRecord()) {
            return RecordNode::class;
        }

        if ($this->isValue()) {
            return ValueNode::class;
        }

        throw new InvalidNodeType();
    }
}

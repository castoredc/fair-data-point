<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Model;

use App\Entity\Enum\NodeType;

interface Node
{
    public function getId(): string;

    public function getTitle(): string;

    public function getType(): ?NodeType;

    public function getValue(): ?string;

    public function getTriples(): array;

    public function hasTriples(): bool;
}

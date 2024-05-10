<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;

class UpdateDataDictionaryGroupCommand
{
    public function __construct(private DataDictionaryGroup $group, private string $title, private int $order, private bool $isRepeated, private bool $isDependent, private ?DependencyGroup $dependencies = null)
    {
    }

    public function getGroup(): DataDictionaryGroup
    {
        return $this->group;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }

    public function isDependent(): bool
    {
        return $this->isDependent;
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }
}

<?php
declare(strict_types=1);

namespace App\Command\Data\DataDictionary;

use App\Entity\Data\DataDictionary\DataDictionaryGroup;
use App\Entity\Data\DataSpecification\Dependency\DependencyGroup;

class UpdateDataDictionaryGroupCommand
{
    private DataDictionaryGroup $group;

    private string $title;

    private int $order;

    private bool $isRepeated;

    private bool $isDependent;

    private ?DependencyGroup $dependencies = null;

    public function __construct(DataDictionaryGroup $group, string $title, int $order, bool $isRepeated, bool $isDependent, ?DependencyGroup $dependencies)
    {
        $this->group = $group;
        $this->title = $title;
        $this->order = $order;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
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

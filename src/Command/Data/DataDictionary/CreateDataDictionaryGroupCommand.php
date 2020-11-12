<?php
declare(strict_types=1);

namespace App\Command\Data\DataDictionary;

use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyGroup;

class CreateDataDictionaryGroupCommand
{
    private DataDictionaryVersion $dataDictionaryVersion;

    private string $title;

    private int $order;

    private bool $isRepeated;

    private bool $isDependent;

    private ?DataDictionaryDependencyGroup $dependencies = null;

    public function __construct(DataDictionaryVersion $dataDictionaryVersion, string $title, int $order, bool $isRepeated, bool $isDependent, ?DataDictionaryDependencyGroup $dependencies)
    {
        $this->dataDictionaryVersion = $dataDictionaryVersion;
        $this->title = $title;
        $this->order = $order;
        $this->isRepeated = $isRepeated;
        $this->isDependent = $isDependent;
        $this->dependencies = $dependencies;
    }

    public function getDataDictionaryVersion(): DataDictionaryVersion
    {
        return $this->dataDictionaryVersion;
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

    public function getDependencies(): ?DataDictionaryDependencyGroup
    {
        return $this->dependencies;
    }
}

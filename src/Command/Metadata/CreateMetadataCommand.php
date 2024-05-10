<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\LocalizedText;

abstract class CreateMetadataCommand
{
    /** @param Agent[] $publishers */
    public function __construct(private ?LocalizedText $title = null, private ?LocalizedText $description = null, private ?string $language = null, private ?string $license = null, private VersionType $versionUpdate, private array $publishers)
    {
    }

    public function getTitle(): ?LocalizedText
    {
        return $this->title;
    }

    public function getDescription(): ?LocalizedText
    {
        return $this->description;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function getVersionUpdate(): VersionType
    {
        return $this->versionUpdate;
    }

    /** @return Agent[] */
    public function getPublishers(): array
    {
        return $this->publishers;
    }
}

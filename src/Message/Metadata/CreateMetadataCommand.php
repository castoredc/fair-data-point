<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\LocalizedText;

abstract class CreateMetadataCommand
{
    /** @var LocalizedText|null */
    private $title;

    /** @var LocalizedText|null */
    private $description;

    /** @var string|null */
    private $language;

    /** @var string|null */
    private $license;

    /** @var VersionType */
    private $versionUpdate;

    /** @var Agent[] */
    private $publishers;

    /** @param Agent[] $publishers */
    public function __construct(?LocalizedText $title, ?LocalizedText $description, ?string $language, ?string $license, VersionType $versionUpdate, array $publishers)
    {
        $this->title = $title;
        $this->description = $description;
        $this->language = $language;
        $this->license = $license;
        $this->versionUpdate = $versionUpdate;
        $this->publishers = $publishers;
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

    /**
     * @return Agent[]
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }
}

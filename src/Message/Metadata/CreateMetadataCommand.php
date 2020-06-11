<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Study;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyType;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\LocalizedText;
use App\Security\CastorUser;
use DateTimeImmutable;

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

    public function __construct(?LocalizedText $title, ?LocalizedText $description, ?string $language, ?string $license, VersionType $versionUpdate)
    {
        $this->title = $title;
        $this->description = $description;
        $this->language = $language;
        $this->license = $license;
        $this->versionUpdate = $versionUpdate;
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
}

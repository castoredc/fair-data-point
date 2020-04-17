<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\FAIRData\Distribution\Distribution;
use App\Security\CastorUser;

class UpdateDistributionCommand
{
    /** @var Distribution */
    private $distribution;

    /** @var string */
    private $slug;

    /** @var string */
    private $title;

    /** @var string */
    private $version;

    /** @var string */
    private $description;

    /** @var string */
    private $language;

    /** @var string */
    private $license;

    /** @var int */
    private $accessRights;

    /** @var bool|null */
    private $includeAllData;

    /** @var CastorUser */
    private $user;

    public function __construct(
        Distribution $distribution,
        string $slug,
        string $title,
        string $version,
        string $description,
        string $language,
        string $license,
        int $accessRights,
        ?bool $includeAllData,
        CastorUser $user
    ) {
        $this->distribution = $distribution;
        $this->slug = $slug;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->language = $language;
        $this->license = $license;
        $this->accessRights = $accessRights;
        $this->includeAllData = $includeAllData;
        $this->user = $user;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getAccessRights(): int
    {
        return $this->accessRights;
    }

    public function getIncludeAllData(): ?bool
    {
        return $this->includeAllData;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}

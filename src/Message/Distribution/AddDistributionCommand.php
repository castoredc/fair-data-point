<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\FAIRData\Dataset;
use App\Security\CastorUser;

class AddDistributionCommand
{
    /** @var string */
    private $type;

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

    /** @var Dataset */
    private $dataset;

    /** @var bool|null */
    private $includeAllData;

    /** @var CastorUser */
    private $user;

    public function __construct(
        string $type,
        string $slug,
        string $title,
        string $version,
        string $description,
        string $language,
        string $license,
        int $accessRights,
        ?bool $includeAllData,
        Dataset $dataset,
        CastorUser $user
    ) {
        $this->type = $type;
        $this->slug = $slug;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->language = $language;
        $this->license = $license;
        $this->accessRights = $accessRights;
        $this->includeAllData = $includeAllData;
        $this->dataset = $dataset;
        $this->user = $user;
    }

    public function getType(): string
    {
        return $this->type;
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

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}

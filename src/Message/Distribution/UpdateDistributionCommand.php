<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

class UpdateDistributionCommand
{
    /** @var Distribution */
    private $distribution;

    /** @var string */
    private $slug;

    /** @var string */
    private $license;

    /** @var int */
    private $accessRights;

    /** @var bool|null */
    private $includeAllData;

    /** @var string|null */
    private $dataModel;

    /** @var string|null */
    private $apiUser;

    /** @var SensitiveDataString|null */
    private $clientId;

    /** @var SensitiveDataString|null */
    private $clientSecret;

    /** @var bool */
    private $published;

    public function __construct(
        Distribution $distribution,
        string $slug,
        string $license,
        int $accessRights,
        ?bool $includeAllData,
        ?string $dataModel,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        bool $published
    ) {
        $this->distribution = $distribution;
        $this->slug = $slug;
        $this->license = $license;
        $this->accessRights = $accessRights;
        $this->includeAllData = $includeAllData;
        $this->dataModel = $dataModel;
        $this->apiUser = $apiUser;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->published = $published;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getSlug(): string
    {
        return $this->slug;
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

    public function getDataModel(): ?string
    {
        return $this->dataModel;
    }

    public function getApiUser(): ?string
    {
        return $this->apiUser;
    }

    public function getClientId(): ?SensitiveDataString
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?SensitiveDataString
    {
        return $this->clientSecret;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}

<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Encryption\SensitiveDataString;
use App\Entity\Enum\DistributionType;
use App\Entity\FAIRData\Dataset;

class CreateDistributionCommand
{
    private DistributionType $type;

    private string $slug;

    private string $license;

    private Dataset $dataset;

    private int $accessRights;

    private ?bool $includeAllData = null;

    private ?string $dataModel = null;

    private ?string $apiUser = null;

    private ?SensitiveDataString $clientId = null;

    private ?SensitiveDataString $clientSecret = null;

    public function __construct(
        DistributionType $type,
        string $slug,
        string $license,
        Dataset $dataset,
        int $accessRights,
        ?bool $includeAllData,
        ?string $dataModel,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret
    ) {
        $this->type = $type;
        $this->slug = $slug;
        $this->license = $license;
        $this->dataset = $dataset;
        $this->accessRights = $accessRights;
        $this->includeAllData = $includeAllData;
        $this->dataModel = $dataModel;
        $this->apiUser = $apiUser;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getType(): DistributionType
    {
        return $this->type;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
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
}

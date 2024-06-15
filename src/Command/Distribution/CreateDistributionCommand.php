<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Dataset;

abstract class CreateDistributionCommand
{
    public function __construct(
        private string $slug,
        private string $defaultMetadataModelId,
        private string $license,
        private Dataset $dataset,
        private ?string $apiUser = null,
        private ?SensitiveDataString $clientId = null,
        private ?SensitiveDataString $clientSecret = null,
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDefaultMetadataModelId(): string
    {
        return $this->defaultMetadataModelId;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
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

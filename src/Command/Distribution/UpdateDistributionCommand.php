<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

abstract class UpdateDistributionCommand
{
    public function __construct(
        private Distribution $distribution,
        private string $slug,
        private string $defaultMetadataModelId,
        private string $license,
        private ?string $apiUser = null,
        private ?SensitiveDataString $clientId = null,
        private ?SensitiveDataString $clientSecret = null,
        private bool $published,
        private bool $cached,
        private bool $public,
    ) {
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
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

    public function isCached(): bool
    {
        return $this->cached;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }
}

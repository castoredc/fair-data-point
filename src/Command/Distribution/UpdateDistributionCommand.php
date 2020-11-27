<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

abstract class UpdateDistributionCommand
{
    private Distribution $distribution;

    private string $slug;

    private string $license;

    private int $accessRights;

    private ?string $apiUser = null;

    private ?SensitiveDataString $clientId = null;

    private ?SensitiveDataString $clientSecret = null;

    private bool $published;

    public function __construct(
        Distribution $distribution,
        string $slug,
        string $license,
        int $accessRights,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        bool $published
    ) {
        $this->distribution = $distribution;
        $this->slug = $slug;
        $this->license = $license;
        $this->accessRights = $accessRights;
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

<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Security\CastorUser;

class AddDistributionContentsCommand
{
    /** @var Distribution */
    private $distribution;

    /** @var string */
    private $type;

    /** @var int */
    private $accessRights;

    /** @var bool|null */
    private $includeAllData;

    /** @var CastorUser */
    private $user;

    public function __construct(
        Distribution $distribution,
        string $type,
        int $accessRights,
        ?bool $includeAllData,
        CastorUser $user
    ) {
        $this->distribution = $distribution;
        $this->type = $type;
        $this->accessRights = $accessRights;
        $this->includeAllData = $includeAllData;
        $this->user = $user;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getType(): string
    {
        return $this->type;
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

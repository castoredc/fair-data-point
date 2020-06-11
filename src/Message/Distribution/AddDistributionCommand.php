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
    private $license;

    /** @var Dataset */
    private $dataset;

    /** @var CastorUser */
    private $user;

    public function __construct(
        string $type,
        string $slug,
        string $license,
        Dataset $dataset,
        CastorUser $user
    ) {
        $this->type = $type;
        $this->slug = $slug;
        $this->license = $license;
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

    public function getLicense(): string
    {
        return $this->license;
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

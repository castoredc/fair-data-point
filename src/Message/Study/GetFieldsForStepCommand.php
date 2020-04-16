<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Castor\Study;
use App\Security\CastorUser;

class GetFieldsForStepCommand
{
    /** @var Study */
    private $study;

    /** @var string */
    private $stepId;

    /** @var CastorUser */
    private $user;

    public function __construct(
        Study $study,
        string $stepId,
        CastorUser $user
    ) {
        $this->study = $study;
        $this->stepId = $stepId;
        $this->user = $user;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getStepId(): string
    {
        return $this->stepId;
    }

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}

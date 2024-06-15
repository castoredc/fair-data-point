<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Castor\CastorStudy;

class GetFieldsForStepCommand
{
    public function __construct(private CastorStudy $study, private string $stepId)
    {
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getStepId(): string
    {
        return $this->stepId;
    }
}

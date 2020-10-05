<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Castor\CastorStudy;

class GetFieldsForStepCommand
{
    private CastorStudy $study;

    private string $stepId;

    public function __construct(CastorStudy $study, string $stepId)
    {
        $this->study = $study;
        $this->stepId = $stepId;
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

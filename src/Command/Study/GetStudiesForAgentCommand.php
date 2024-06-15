<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\FAIRData\Agent\Agent;

class GetStudiesForAgentCommand
{
    public function __construct(private Agent $agent)
    {
    }

    public function getAgent(): Agent
    {
        return $this->agent;
    }
}

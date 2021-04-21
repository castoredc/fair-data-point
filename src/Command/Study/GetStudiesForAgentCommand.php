<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\FAIRData\Agent\Agent;

class GetStudiesForAgentCommand
{
    private Agent $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function getAgent(): Agent
    {
        return $this->agent;
    }
}

<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\FAIRData\Agent\Agent;

class GetAgentAssociatedMetadataCountCommand
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

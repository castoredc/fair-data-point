<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\FAIRData\Agent\Agent;

class GetAgentAssociatedMetadataCountCommand
{
    public function __construct(private Agent $agent)
    {
    }

    public function getAgent(): Agent
    {
        return $this->agent;
    }
}

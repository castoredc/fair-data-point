<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Study;

class AddStudyCenterCommand
{
    public function __construct(private Study $study, private string $organizationId)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }
}

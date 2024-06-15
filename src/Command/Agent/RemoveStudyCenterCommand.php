<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Study;

class RemoveStudyCenterCommand
{
    public function __construct(private Study $study, private string $id)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

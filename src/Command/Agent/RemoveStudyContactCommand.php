<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Study;

class RemoveStudyContactCommand
{
    private Study $study;

    private string $id;

    public function __construct(Study $study, string $id)
    {
        $this->study = $study;
        $this->id = $id;
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

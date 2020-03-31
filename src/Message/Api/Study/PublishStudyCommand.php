<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Entity\Castor\Study;

class PublishStudyCommand
{
    /** @var Study */
    private $study;

    public function __construct(Study $study)
    {
        $this->study = $study;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }
}

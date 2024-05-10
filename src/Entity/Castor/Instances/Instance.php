<?php
declare(strict_types=1);

namespace App\Entity\Castor\Instances;

use App\Entity\Castor\Record;
use DateTime;

abstract class Instance
{
    public function __construct(protected string $id, protected Record $record, protected DateTime $createdOn)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }
}

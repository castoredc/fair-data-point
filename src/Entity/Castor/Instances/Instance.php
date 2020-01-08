<?php
declare(strict_types=1);

namespace App\Entity\Castor\Instances;

use App\Entity\Castor\Record;
use DateTime;

abstract class Instance
{
    /** @var string */
    protected $id;

    /** @var Record */
    protected $record;

    /** @var DateTime */
    protected $createdOn;

    public function __construct(string $id, Record $record, DateTime $createdOn)
    {
        $this->id = $id;
        $this->record = $record;
        $this->createdOn = $createdOn;
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

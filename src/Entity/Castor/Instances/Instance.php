<?php


namespace App\Entity\Castor\Instances;


use App\Entity\Castor\Record;
use DateTime;

class Instance
{
    /** @var string */
    protected $id;

    /** @var Record */
    protected $record;

    /** @var DateTime */
    protected $createdOn;

    /**
     * Instance constructor.
     * @param string $id
     * @param Record $record
     * @param DateTime $createdOn
     */
    public function __construct(string $id, Record $record, DateTime $createdOn)
    {
        $this->id = $id;
        $this->record = $record;
        $this->createdOn = $createdOn;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

}
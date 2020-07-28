<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\RecordDataCollection;
use DateTimeImmutable;

class Record
{
    /** @var CastorStudy */
    private $study;

    /** @var string */
    private $recordId;

    /** @var RecordDataCollection */
    private $data;

    /** @var DateTimeImmutable */
    private $createdOn;

    /** @var DateTimeImmutable */
    private $updatedOn;

    public function __construct(CastorStudy $study, string $recordId, DateTimeImmutable $createdOn, DateTimeImmutable $updatedOn)
    {
        $this->study = $study;
        $this->recordId = $recordId;
        $this->createdOn = $createdOn;
        $this->updatedOn = $updatedOn;
    }

    public function getRecordId(): string
    {
        return $this->recordId;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getData(): RecordDataCollection
    {
        return $this->data;
    }

    public function setData(RecordDataCollection $data): void
    {
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->recordId;
    }

    public function getCreatedOn(): DateTimeImmutable
    {
        return $this->createdOn;
    }

    public function getUpdatedOn(): DateTimeImmutable
    {
        return $this->updatedOn;
    }
}

<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\RecordDataCollection;
use DateTimeImmutable;

class Record
{
    /** @var string */
    private $recordId;

    /** @var RecordDataCollection */
    private $data;

    /** @var DateTimeImmutable */
    private $createdOn;

    /** @var DateTimeImmutable */
    private $updatedOn;

    public function __construct(string $recordId, DateTimeImmutable $createdOn, DateTimeImmutable $updatedOn)
    {
        $this->recordId = $recordId;
        $this->createdOn = $createdOn;
        $this->updatedOn = $updatedOn;
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

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Record
    {
        return new Record(
            $data['record_id'],
            DateTimeImmutable::__set_state($data['created_on']),
            DateTimeImmutable::__set_state($data['updated_on']),
        );
    }
}

<?php
declare(strict_types=1);

namespace App\Entity\Castor;

class Record
{
    /** @var string */
    private $recordId;

    /** @var RecordDataCollection */
    private $data;

    public function __construct(string $recordId)
    {
        $this->recordId = $recordId;
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

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Record
    {
        return new Record(
            $data['record_id']
        );
    }
}

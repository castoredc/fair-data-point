<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\Log\DistributionGenerationRecordLog;
use const DATE_ATOM;

class DistributionGenerationRecordLogApiResource implements ApiResource
{
    public function __construct(private DistributionGenerationRecordLog $recordLog)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->recordLog->getId(),
            'createdAt' => $this->recordLog->getCreatedAt()->format(DATE_ATOM),
            'status' => $this->recordLog->getStatus()->toString(),
            'errors' => $this->recordLog->getErrors(),
            'record' => [
                'id' => $this->recordLog->getRecord()->getId(),
                'institute' => $this->recordLog->getRecord()->getInstitute()->getName(),
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\Log\DistributionGenerationLog;
use const DATE_ATOM;

class DistributionGenerationLogApiResource implements ApiResource
{
    private DistributionGenerationLog $log;

    public function __construct(DistributionGenerationLog $log)
    {
        $this->log = $log;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->log->getId(),
            'createdAt' => $this->log->getCreatedAt()->format(DATE_ATOM),
            'status' => $this->log->getStatus()->toString(),
            'errors' => $this->log->getErrors(),
            'records' => [
                'total' => $this->log->getTotalRecordCount(),
                'success' => $this->log->getSuccessRecordCount(),
                'not_updated' => $this->log->getNotUpdatedRecordCount(),
                'error' => $this->log->getErrorRecordCount(),
            ],
        ];
    }
}

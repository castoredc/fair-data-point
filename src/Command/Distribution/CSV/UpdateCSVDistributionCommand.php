<?php
declare(strict_types=1);

namespace App\Command\Distribution\CSV;

use App\Command\Distribution\UpdateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

class UpdateCSVDistributionCommand extends UpdateDistributionCommand
{
    public function __construct(
        Distribution $distribution,
        string $slug,
        string $defaultMetadataModelId,
        string $license,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        bool $published,
        bool $cached,
        bool $public,
        private string $dataDictionaryId,
        private string $dataDictionaryVersionId,
    ) {
        parent::__construct(
            $distribution,
            $slug,
            $defaultMetadataModelId,
            $license,
            $apiUser,
            $clientId,
            $clientSecret,
            $published,
            $cached,
            $public
        );
    }

    public function getDataDictionaryId(): string
    {
        return $this->dataDictionaryId;
    }

    public function getDataDictionaryVersionId(): string
    {
        return $this->dataDictionaryVersionId;
    }
}

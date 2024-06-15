<?php
declare(strict_types=1);

namespace App\Command\Distribution\CSV;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Dataset;

class CreateCSVDistributionCommand extends CreateDistributionCommand
{
    public function __construct(
        string $slug,
        string $defaultMetadataModelId,
        string $license,
        Dataset $dataset,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        private string $dataDictionaryId,
        private string $dataDictionaryVersionId,
    ) {
        parent::__construct($slug, $defaultMetadataModelId, $license, $dataset, $apiUser, $clientId, $clientSecret);
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

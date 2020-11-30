<?php
declare(strict_types=1);

namespace App\Command\Distribution\CSV;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Dataset;

class CreateCSVDistributionCommand extends CreateDistributionCommand
{
    private string $dataDictionaryId;

    private string $dataDictionaryVersionId;

    public function __construct(
        string $slug,
        string $license,
        Dataset $dataset,
        int $accessRights,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        string $dataDictionaryId,
        string $dataDictionaryVersionId
    ) {
        parent::__construct($slug, $license, $dataset, $accessRights, $apiUser, $clientId, $clientSecret);

        $this->dataDictionaryId = $dataDictionaryId;
        $this->dataDictionaryVersionId = $dataDictionaryVersionId;
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

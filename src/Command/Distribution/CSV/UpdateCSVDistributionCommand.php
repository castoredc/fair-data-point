<?php
declare(strict_types=1);

namespace App\Command\Distribution\CSV;

use App\Command\Distribution\UpdateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

class UpdateCSVDistributionCommand extends UpdateDistributionCommand
{
    private string $dataDictionaryId;

    private string $dataDictionaryVersionId;

    public function __construct(
        Distribution $distribution,
        string $slug,
        string $license,
        int $accessRights,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        bool $published,
        string $dataDictionaryId,
        string $dataDictionaryVersionId
    ) {
        parent::__construct($distribution, $slug, $license, $accessRights, $apiUser, $clientId, $clientSecret, $published);

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

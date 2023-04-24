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
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        bool $published,
        bool $cached,
        bool $public,
        string $dataDictionaryId,
        string $dataDictionaryVersionId
    ) {
        parent::__construct($distribution, $slug, $license, $apiUser, $clientId, $clientSecret, $published, $cached, $public);

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

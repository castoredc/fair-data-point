<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Command\Distribution\UpdateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

class UpdateRDFDistributionCommand extends UpdateDistributionCommand
{
    private string $dataModelId;

    private string $dataModelVersionId;

    public function __construct(
        Distribution $distribution,
        string $slug,
        string $license,
        int $accessRights,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        bool $published,
        string $dataModelId,
        string $dataModelVersionId
    ) {
        parent::__construct($distribution, $slug, $license, $accessRights, $apiUser, $clientId, $clientSecret, $published);

        $this->dataModelId = $dataModelId;
        $this->dataModelVersionId = $dataModelVersionId;
    }

    public function getDataModelId(): string
    {
        return $this->dataModelId;
    }

    public function getDataModelVersionId(): string
    {
        return $this->dataModelVersionId;
    }
}

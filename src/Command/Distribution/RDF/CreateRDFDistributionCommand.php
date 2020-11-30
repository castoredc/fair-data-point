<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Dataset;

class CreateRDFDistributionCommand extends CreateDistributionCommand
{
    private string $dataModelId;

    private string $dataModelVersionId;

    public function __construct(
        string $slug,
        string $license,
        Dataset $dataset,
        int $accessRights,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        string $dataModelId,
        string $dataModelVersionId
    ) {
        parent::__construct($slug, $license, $dataset, $accessRights, $apiUser, $clientId, $clientSecret);

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

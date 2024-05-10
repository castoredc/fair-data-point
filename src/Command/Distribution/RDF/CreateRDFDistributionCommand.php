<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Dataset;

class CreateRDFDistributionCommand extends CreateDistributionCommand
{
    public function __construct(
        string $slug,
        string $defaultMetadataModelId,
        string $license,
        Dataset $dataset,
        ?string $apiUser,
        ?SensitiveDataString $clientId,
        ?SensitiveDataString $clientSecret,
        private string $dataModelId,
        private string $dataModelVersionId,
    ) {
        parent::__construct($slug, $defaultMetadataModelId, $license, $dataset, $apiUser, $clientId, $clientSecret);
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

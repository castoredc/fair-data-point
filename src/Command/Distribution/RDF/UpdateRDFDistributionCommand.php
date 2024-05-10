<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

use App\Command\Distribution\UpdateDistributionCommand;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;

class UpdateRDFDistributionCommand extends UpdateDistributionCommand
{
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
        private string $dataModelId,
        private string $dataModelVersionId,
    ) {
        parent::__construct($distribution, $slug, $license, $apiUser, $clientId, $clientSecret, $published, $cached, $public);
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

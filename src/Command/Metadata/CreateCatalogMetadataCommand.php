<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Catalog;

class CreateCatalogMetadataCommand extends CreateMetadataCommand
{
    public function __construct(
        private readonly Catalog $catalog,
        VersionType $versionType,
        string $modelId,
        string $modelVersionId,
    ) {
        parent::__construct($versionType, $modelId, $modelVersionId);
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}

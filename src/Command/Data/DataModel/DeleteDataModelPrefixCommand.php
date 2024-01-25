<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\DataSpecification\DataModel\NamespacePrefix;

class DeleteDataModelPrefixCommand
{
    private NamespacePrefix $dataModelPrefix;

    public function __construct(NamespacePrefix $dataModelPrefix)
    {
        $this->dataModelPrefix = $dataModelPrefix;
    }

    public function getDataModelPrefix(): NamespacePrefix
    {
        return $this->dataModelPrefix;
    }
}

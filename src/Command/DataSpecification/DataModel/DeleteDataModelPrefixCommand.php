<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\NamespacePrefix;

class DeleteDataModelPrefixCommand
{
    public function __construct(private NamespacePrefix $dataModelPrefix)
    {
    }

    public function getDataModelPrefix(): NamespacePrefix
    {
        return $this->dataModelPrefix;
    }
}

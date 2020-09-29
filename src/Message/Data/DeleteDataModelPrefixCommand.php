<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\NamespacePrefix;

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

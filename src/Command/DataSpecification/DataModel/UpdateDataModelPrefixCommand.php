<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelPrefixCommand;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;

class UpdateDataModelPrefixCommand extends UpdateModelPrefixCommand
{
    private NamespacePrefix $dataModelPrefix;

    public function __construct(NamespacePrefix $dataModelPrefix, string $prefix, string $uri)
    {
        parent::__construct($prefix, $uri);

        $this->dataModelPrefix = $dataModelPrefix;
    }

    public function getDataModelPrefix(): NamespacePrefix
    {
        return $this->dataModelPrefix;
    }
}

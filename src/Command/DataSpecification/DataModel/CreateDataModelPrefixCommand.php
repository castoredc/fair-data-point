<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\CreateModelPrefixCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class CreateDataModelPrefixCommand extends CreateModelPrefixCommand
{
    public function __construct(private DataModelVersion $dataModelVersion, string $prefix, string $uri)
    {
        parent::__construct($prefix, $uri);
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}

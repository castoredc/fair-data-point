<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelVersion;

class CreateDataModelPrefixCommand
{
    private DataModelVersion $dataModelVersion;

    private string $prefix;

    private string $uri;

    public function __construct(DataModelVersion $dataModelVersion, string $prefix, string $uri)
    {
        $this->dataModelVersion = $dataModelVersion;
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}

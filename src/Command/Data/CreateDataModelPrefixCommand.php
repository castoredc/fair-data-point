<?php
declare(strict_types=1);

namespace App\Command\Data;

use App\Entity\Data\DataModel\DataModelVersion;

class CreateDataModelPrefixCommand
{
    private DataModelVersion $dataModel;

    private string $prefix;

    private string $uri;

    public function __construct(DataModelVersion $dataModel, string $prefix, string $uri)
    {
        $this->dataModel = $dataModel;
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
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

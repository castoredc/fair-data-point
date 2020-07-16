<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelVersion;

class CreateDataModelPrefixCommand
{
    /** @var DataModelVersion */
    private $dataModel;

    /** @var string */
    private $prefix;

    /** @var string */
    private $uri;

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

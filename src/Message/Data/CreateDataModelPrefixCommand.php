<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModel;

class CreateDataModelPrefixCommand
{
    /** @var DataModel */
    private $dataModel;

    /** @var string */
    private $prefix;

    /** @var string */
    private $uri;

    public function __construct(DataModel $dataModel, string $prefix, string $uri)
    {
        $this->dataModel = $dataModel;
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getDataModel(): DataModel
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

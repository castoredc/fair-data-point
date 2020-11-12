<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\Data\DataModel\NamespacePrefix;

class UpdateDataModelPrefixCommand
{
    private NamespacePrefix $dataModelPrefix;

    private string $prefix;

    private string $uri;

    public function __construct(NamespacePrefix $dataModelPrefix, string $prefix, string $uri)
    {
        $this->dataModelPrefix = $dataModelPrefix;
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getDataModelPrefix(): NamespacePrefix
    {
        return $this->dataModelPrefix;
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

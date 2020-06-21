<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\NamespacePrefix;

class UpdateDataModelPrefixCommand
{
    /** @var NamespacePrefix */
    private $dataModelPrefix;

    /** @var string */
    private $prefix;

    /** @var string */
    private $uri;

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

<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class UpdateModelPrefixCommand
{
    private string $prefix;

    private string $uri;

    public function __construct(string $prefix, string $uri)
    {
        $this->prefix = $prefix;
        $this->uri = $uri;
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

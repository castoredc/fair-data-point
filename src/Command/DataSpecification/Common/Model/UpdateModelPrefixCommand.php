<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class UpdateModelPrefixCommand
{
    public function __construct(private string $prefix, private string $uri)
    {
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

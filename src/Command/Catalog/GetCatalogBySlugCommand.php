<?php
declare(strict_types=1);

namespace App\Command\Catalog;

class GetCatalogBySlugCommand
{
    public function __construct(private string $slug)
    {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}

<?php
declare(strict_types=1);

namespace App\Message\Catalog;

class GetCatalogBySlugCommand
{
    private string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}

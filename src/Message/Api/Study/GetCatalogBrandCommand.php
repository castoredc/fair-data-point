<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Security\CastorUser;

class GetCatalogBrandCommand
{
    /** @var string */
    private $slug;

    /**
     * GetCatalogCommand constructor.
     *
     * @param string $slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}

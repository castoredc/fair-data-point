<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CatalogNotFound extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Catalog not found.'];
    }
}

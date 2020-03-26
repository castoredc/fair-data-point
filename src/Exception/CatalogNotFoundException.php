<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CatalogNotFoundException extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Catalog not found.'];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CountryNotFoundException extends Exception
{
    public function toArray(): array
    {
        return [
            'error' => 'Country not found.'
        ];
    }
}

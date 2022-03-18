<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class NoFieldsFound extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The structure does not contain fields.'];
    }
}

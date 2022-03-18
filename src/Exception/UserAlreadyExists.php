<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class UserAlreadyExists extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'This user is already added.'];
    }
}

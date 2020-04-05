<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class UserNotFound extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'The user is not found.'];
    }
}

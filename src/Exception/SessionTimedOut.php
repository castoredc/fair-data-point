<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class SessionTimedOut extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Your session timed out, please log in again'];
    }
}

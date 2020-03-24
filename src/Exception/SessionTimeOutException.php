<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class SessionTimeOutException extends Exception
{
    public function toArray(): array
    {
        return [
            'error' => 'Your session timed out, please log in again'
        ];
    }
}

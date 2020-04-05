<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class UserNotACastorUser extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'The current user is not a Castor User.'];
    }
}

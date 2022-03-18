<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidAgentType extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The type of agent should be the same.'];
    }
}

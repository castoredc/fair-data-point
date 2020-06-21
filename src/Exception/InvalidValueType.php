<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidValueType extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'The value assigned to this triple is not compatible with the assigned field.'];
    }
}

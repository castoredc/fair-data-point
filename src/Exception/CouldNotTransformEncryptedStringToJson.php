<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CouldNotTransformEncryptedStringToJson extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Failed to create a json string out of the encrypted string.'];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CouldNotDecrypt extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'Failed to decrypt given ciphertext.'];
    }
}

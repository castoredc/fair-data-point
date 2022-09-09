<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class ErrorFetchingStardogData extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'error' => 'An error occurred while getting data from the triple store.',
            'details' => $this->message,
        ];
    }
}

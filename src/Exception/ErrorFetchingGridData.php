<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class ErrorFetchingGridData extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'error' => 'An error occurred while getting data from GRID.',
            'details' => $this->message,
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class ErrorFetchingCastorData extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'error' => 'An error occurred while getting data from Castor.',
            'details' => $this->message,
        ];
    }
}

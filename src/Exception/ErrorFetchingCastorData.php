<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class ErrorFetchingCastorData extends Exception
{
    /** @var string */
    protected $message;

    public function __construct(string $message)
    {
        parent::__construct();

        $this->message = $message;
    }

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

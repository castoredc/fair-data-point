<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CouldNotCreateDatabase extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Could not create database.'];
    }
}

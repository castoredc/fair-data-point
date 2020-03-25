<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class StudyNotFoundException extends Exception
{
    public function toArray(): array
    {
        return [
            'error' => 'Study not found.'
        ];
    }
}

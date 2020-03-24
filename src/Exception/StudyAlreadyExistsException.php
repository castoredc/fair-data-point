<?php

namespace App\Exception;

use Exception;

class StudyAlreadyExistsException extends Exception
{
    public function toArray(): array
    {
        return [
            'error' => 'This study is already imported.'
        ];
    }
}
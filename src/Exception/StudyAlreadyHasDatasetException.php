<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class StudyAlreadyHasDatasetException extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'This study already has a dataset assigned.'];
    }
}

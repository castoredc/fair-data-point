<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class StudyAlreadyHasSameDataset extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'This study already has this dataset assigned.'];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class NoAccessPermissionToStudy extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'You do not have permission to access this study.'];
    }
}

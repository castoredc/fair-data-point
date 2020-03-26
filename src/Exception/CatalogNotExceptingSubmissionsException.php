<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CatalogNotExceptingSubmissionsException extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'This catalog is not accepting submissions.'];
    }
}

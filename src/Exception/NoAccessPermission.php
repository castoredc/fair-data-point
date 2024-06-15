<?php
declare(strict_types=1);

namespace App\Exception;

class NoAccessPermission extends RenderableApiException
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'You do not have permission to access this data.'];
    }
}

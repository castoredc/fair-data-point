<?php
declare(strict_types=1);

namespace App\Exception;

class NotFound extends RenderableApiException
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The data you are trying to access cannot be found.'];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

abstract class RenderableApiException extends Exception implements ApiException
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'error' => $this->message,
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class InvalidFile extends RenderableApiException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}

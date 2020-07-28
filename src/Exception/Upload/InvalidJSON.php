<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class InvalidJSON extends RenderableApiException
{
    /** @var string */
    protected $message = 'Cannot parse the JSON.';
}

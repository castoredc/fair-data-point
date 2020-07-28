<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class EmptyFile extends RenderableApiException
{
    /** @var string */
    protected $message = 'The uploaded file is empty.';
}

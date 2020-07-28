<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class NoFileSpecified extends RenderableApiException
{
    /** @var string */
    protected $message = 'No file specified.';
}

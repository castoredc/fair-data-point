<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class NoFileSpecified extends RenderableApiException
{
    /** @inheritDoc */
    protected $message = 'No file specified.';
}

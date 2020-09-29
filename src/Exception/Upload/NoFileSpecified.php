<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class NoFileSpecified extends RenderableApiException
{
    public function __construct()
    {
        parent::__construct('No file specified.');
    }
}

<?php
declare(strict_types=1);

namespace App\Exception\Upload;

use App\Exception\RenderableApiException;

class EmptyFile extends RenderableApiException
{
    public function __construct()
    {
        parent::__construct('The uploaded file is empty.');
    }
}

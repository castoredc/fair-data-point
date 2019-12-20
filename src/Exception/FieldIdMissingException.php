<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FieldIdMissingException extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('Please provide a field ID');
    }
}

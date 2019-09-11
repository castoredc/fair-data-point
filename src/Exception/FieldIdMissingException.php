<?php
/**
 * Created by PhpStorm.
 * User: Martijn
 * Date: 21/06/2018
 * Time: 15:46
 */

namespace App\Exception;


use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FieldIdMissingException extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('Please provide a field ID');
    }
}
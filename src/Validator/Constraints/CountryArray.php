<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class CountryArray extends Constraint
{
    public string $message = 'This list does not contain valid countries.';
}

<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/** @Annotation */
#[Attribute]
class OptionGroupOptions extends Constraint
{
    public string $title = 'Please enter a name';
    public string $value = 'Please enter a value';

    public string $validationError = 'Option %number%: %message%';
}

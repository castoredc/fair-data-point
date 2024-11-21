<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class LocalizedText extends Constraint
{
    public string $message = 'This text does not follow the [text, language] format.';
}

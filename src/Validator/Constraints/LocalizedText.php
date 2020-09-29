<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LocalizedText extends Constraint
{
    public string $message = 'This text does not follow the [text, language] format.';
}

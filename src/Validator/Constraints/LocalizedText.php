<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LocalizedText extends Constraint
{
    /** @var string */
    public $message = 'This text does not follow the [text, language] format.';
}

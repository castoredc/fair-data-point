<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function is_array;

class LocalizedTextValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (! $constraint instanceof LocalizedText) {
            throw new UnexpectedTypeException($constraint, LocalizedText::class);
        }

        if ($value === null || $value === []) {
            return;
        }

        if (! is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        foreach ($value as $item) {
            if (isset($item['text']) && isset($item['language'])) {
                continue;
            }

            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function is_array;

class OptionGroupOptionsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof OptionGroupOptions) {
            throw new UnexpectedTypeException($constraint, OptionGroupOptions::class);
        }

        if ($value === null || $value === []) {
            return;
        }

        if (! is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        $collection = new Assert\Collection([
            'allowExtraFields' => true,
            'fields' => [
                'title' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'string']),
                ],
                'value' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'string']),
                ],
            ],
        ]);

        foreach ($value as $index => $option) {
            $this->mergeViolations($index, $option, $collection, $constraint);
        }
    }

    /** @param mixed[] $option */
    private function mergeViolations(int $index, array $option, Assert\Collection $collection, OptionGroupOptions $constraint): void
    {
        $violations = $this->context->getValidator()->validate($option, $collection);

        if ($violations->count() <= 0) {
            return;
        }

        foreach ($violations as $violation) {
            /** @var ConstraintViolation $violation */
            $this->context->buildViolation($constraint->validationError)
                ->setParameter('%number%', (string) ($index + 1))
                ->setParameter('%message%', (string) $violation->getMessage())
                ->addViolation();
        }
    }
}

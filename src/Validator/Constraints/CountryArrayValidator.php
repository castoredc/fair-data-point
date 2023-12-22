<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function is_array;

class CountryArrayValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof CountryArray) {
            throw new UnexpectedTypeException($constraint, CountryArray::class);
        }

        if ($value === null || $value === []) {
            return;
        }

        if (! is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        foreach ($value as $country) {
            if (Countries::exists($country)) {
                continue;
            }

            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ value }}', $this->formatValue($country))
                          ->setCode(Country::NO_SUCH_COUNTRY_ERROR)
                          ->addViolation();
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\unit\Validator\Constraints;

use App\Validator\Constraints\CountryArray;
use App\Validator\Constraints\CountryArrayValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<CountryArrayValidator> */
final class CountryArrayValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): CountryArrayValidator
    {
        return new CountryArrayValidator();
    }

    public function testThrowsForWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate([], new NotBlank());
    }

    public function testThrowsForNonArrayValue(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate('NL', new CountryArray());
    }

    #[DataProvider('provideEmptyValues')]
    public function testNoViolationForNullOrEmptyValue(mixed $value): void
    {
        $this->validator->validate($value, new CountryArray());

        $this->assertNoViolation();
    }

    /** @return array<string, array{mixed}> */
    public static function provideEmptyValues(): array
    {
        return [
            'null' => [null],
            'empty array' => [[]],
        ];
    }

    public function testNoViolationForValidCountryCodes(): void
    {
        $this->validator->validate(['NL', 'DE', 'US'], new CountryArray());

        $this->assertNoViolation();
    }

    #[DataProvider('provideInvalidCountryCodes')]
    public function testViolationForInvalidCountryCode(string $invalidCode): void
    {
        $this->validator->validate([$invalidCode], new CountryArray());

        $this->buildViolation('This list does not contain valid countries.')
            ->setParameter('{{ value }}', '"' . $invalidCode . '"')
            ->setCode(Country::NO_SUCH_COUNTRY_ERROR)
            ->assertRaised();
    }

    /** @return array<string, array{string}> */
    public static function provideInvalidCountryCodes(): array
    {
        return [
            'unknown code' => ['XX'],
            'lowercase code' => ['nl'],
            'country name' => ['Netherlands'],
            'empty string' => [''],
        ];
    }

    public function testViolationIsAddedPerInvalidCountry(): void
    {
        $this->validator->validate(['NL', 'XX', 'YY'], new CountryArray());

        $this->buildViolation('This list does not contain valid countries.')
            ->setParameter('{{ value }}', '"XX"')
            ->setCode(Country::NO_SUCH_COUNTRY_ERROR)
            ->buildNextViolation('This list does not contain valid countries.')
            ->setParameter('{{ value }}', '"YY"')
            ->setCode(Country::NO_SUCH_COUNTRY_ERROR)
            ->assertRaised();
    }
}

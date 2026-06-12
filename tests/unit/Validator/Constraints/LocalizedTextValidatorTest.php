<?php
declare(strict_types=1);

namespace App\Tests\unit\Validator\Constraints;

use App\Validator\Constraints\LocalizedText;
use App\Validator\Constraints\LocalizedTextValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<LocalizedTextValidator> */
final class LocalizedTextValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): LocalizedTextValidator
    {
        return new LocalizedTextValidator();
    }

    public function testThrowsForWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate([], new NotBlank());
    }

    public function testThrowsForNonArrayValue(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate('not an array', new LocalizedText());
    }

    #[DataProvider('provideEmptyValues')]
    public function testNoViolationForNullOrEmptyValue(mixed $value): void
    {
        $this->validator->validate($value, new LocalizedText());

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

    public function testNoViolationForValidLocalizedTexts(): void
    {
        $this->validator->validate(
            [
                ['text' => 'Hello', 'language' => 'en'],
                ['text' => 'Hallo', 'language' => 'nl'],
            ],
            new LocalizedText()
        );

        $this->assertNoViolation();
    }

    /** @param array<array<string, string>> $value */
    #[DataProvider('provideInvalidLocalizedTexts')]
    public function testViolationForInvalidLocalizedText(array $value): void
    {
        $this->validator->validate($value, new LocalizedText());

        $this->buildViolation('This text does not follow the [text, language] format.')
            ->assertRaised();
    }

    /** @return array<string, array{array<array<string, string>>}> */
    public static function provideInvalidLocalizedTexts(): array
    {
        return [
            'missing language' => [[['text' => 'Hello']]],
            'missing text' => [[['language' => 'en']]],
            'missing both' => [[['foo' => 'bar']]],
        ];
    }

    public function testViolationIsAddedPerInvalidItem(): void
    {
        $this->validator->validate(
            [
                ['text' => 'Hello', 'language' => 'en'],
                ['text' => 'No language'],
                ['language' => 'nl'],
            ],
            new LocalizedText()
        );

        $this->buildViolation('This text does not follow the [text, language] format.')
            ->buildNextViolation('This text does not follow the [text, language] format.')
            ->assertRaised();
    }
}

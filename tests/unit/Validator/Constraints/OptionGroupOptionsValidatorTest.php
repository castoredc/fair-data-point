<?php
declare(strict_types=1);

namespace App\Tests\unit\Validator\Constraints;

use App\Validator\Constraints\OptionGroupOptions;
use App\Validator\Constraints\OptionGroupOptionsValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<OptionGroupOptionsValidator> */
final class OptionGroupOptionsValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): OptionGroupOptionsValidator
    {
        return new OptionGroupOptionsValidator();
    }

    public function testThrowsForWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate([], new NotBlank());
    }

    public function testThrowsForNonArrayValue(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate('not an array', new OptionGroupOptions());
    }

    #[DataProvider('provideEmptyValues')]
    public function testNoViolationForNullOrEmptyValue(mixed $value): void
    {
        $this->validator->validate($value, new OptionGroupOptions());

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

    public function testNoViolationForValidOptions(): void
    {
        $this->validator->validate(
            [
                ['title' => 'Yes', 'value' => '1'],
                ['title' => 'No', 'value' => '0'],
            ],
            new OptionGroupOptions()
        );

        $this->assertNoViolation();
    }

    public function testNestedViolationsAreWrappedWithOptionNumber(): void
    {
        // The nested validator call is mocked by ConstraintValidatorTestCase and returns
        // the violations queued here, allowing us to test the wrapping logic in isolation.
        $this->expectViolationsAt(0, '', new NotBlank());

        $this->validator->validate([['title' => '', 'value' => '1']], new OptionGroupOptions());

        $this->buildViolation('Option %number%: %message%')
            ->setParameter('%number%', '1')
            ->setParameter('%message%', 'This value should not be blank.')
            ->assertRaised();
    }

    public function testOptionNumberReflectsPositionInList(): void
    {
        $this->expectViolationsAt(0, 'valid', new NotBlank());
        $this->expectViolationsAt(1, '', new NotBlank());

        $this->validator->validate(
            [
                ['title' => 'Yes', 'value' => '1'],
                ['title' => '', 'value' => '0'],
            ],
            new OptionGroupOptions()
        );

        $this->buildViolation('Option %number%: %message%')
            ->setParameter('%number%', '2')
            ->setParameter('%message%', 'This value should not be blank.')
            ->assertRaised();
    }
}

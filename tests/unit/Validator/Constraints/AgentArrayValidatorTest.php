<?php
declare(strict_types=1);

namespace App\Tests\unit\Validator\Constraints;

use App\Validator\Constraints\AgentArray;
use App\Validator\Constraints\AgentArrayValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<AgentArrayValidator> */
final class AgentArrayValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): AgentArrayValidator
    {
        return new AgentArrayValidator();
    }

    public function testThrowsForWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate([], new NotBlank());
    }

    public function testThrowsForNonArrayValue(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate('not an array', new AgentArray());
    }

    #[DataProvider('provideEmptyValues')]
    public function testNoViolationForNullOrEmptyValue(mixed $value): void
    {
        $this->validator->validate($value, new AgentArray());

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

    /** @param array<array<string, mixed>> $agents */
    #[DataProvider('provideValidAgents')]
    public function testNoViolationForValidAgents(array $agents): void
    {
        $this->validator->validate($agents, new AgentArray());

        $this->assertNoViolation();
    }

    /** @return array<string, array{array<array<string, mixed>>}> */
    public static function provideValidAgents(): array
    {
        return [
            'person' => [
                [
                    [
                        'type' => 'person',
                        'person' => [
                            'firstName' => 'Jane',
                            'lastName' => 'Doe',
                            'email' => 'jane.doe@example.com',
                        ],
                    ],
                ],
            ],
            'organization' => [
                [
                    [
                        'type' => 'organization',
                        'organization' => [
                            'name' => 'Castor',
                            'country' => 'NL',
                            'city' => 'Amsterdam',
                        ],
                    ],
                ],
            ],
            'department' => [
                [
                    [
                        'type' => 'department',
                        'department' => ['name' => 'Research'],
                        'organization' => [
                            'name' => 'Castor',
                            'country' => 'NL',
                            'city' => 'Amsterdam',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testViolationForAgentWithoutType(): void
    {
        $this->validator->validate([['person' => ['firstName' => 'Jane']]], new AgentArray());

        $this->buildViolation('Please specify the type of agent.')
            ->assertRaised();
    }

    public function testViolationForAgentWithInvalidType(): void
    {
        $this->validator->validate([['type' => 'robot']], new AgentArray());

        $this->buildViolation('This agent is of an invalid type.')
            ->assertRaised();
    }

    public function testNestedViolationsAreWrappedWithAgentNumber(): void
    {
        // The nested validator call is mocked by ConstraintValidatorTestCase and returns
        // the violations queued here, allowing us to test the wrapping logic in isolation.
        $this->expectViolationsAt(0, '', new NotBlank());

        $agent = [
            'type' => 'person',
            'person' => [
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'email' => '',
            ],
        ];

        $this->validator->validate([$agent], new AgentArray());

        $this->buildViolation('Agent %number%: %message%')
            ->setParameter('%number%', '1')
            ->setParameter('%message%', 'This value should not be blank.')
            ->assertRaised();
    }

    public function testEveryNestedViolationIsWrapped(): void
    {
        $collection = new Collection([
            'fields' => [
                'name' => new NotBlank(),
                'city' => new NotBlank(),
            ],
        ]);

        $this->expectViolationsAt(0, [], $collection);

        $agent = [
            'type' => 'organization',
            'organization' => [],
        ];

        $this->validator->validate([$agent], new AgentArray());

        $this->buildViolation('Agent %number%: %message%')
            ->setParameter('%number%', '1')
            ->setParameter('%message%', 'This field is missing.')
            ->buildNextViolation('Agent %number%: %message%')
            ->setParameter('%number%', '1')
            ->setParameter('%message%', 'This field is missing.')
            ->assertRaised();
    }

    public function testAgentNumberReflectsPositionInList(): void
    {
        $this->expectViolationsAt(0, '', new NotBlank());
        $this->expectViolationsAt(1, '', new NotBlank());

        $agents = [
            [
                'type' => 'person',
                'person' => ['firstName' => '', 'lastName' => 'Doe', 'email' => 'jane.doe@example.com'],
            ],
            [
                'type' => 'organization',
                'organization' => ['name' => '', 'country' => 'NL', 'city' => 'Amsterdam'],
            ],
        ];

        $this->validator->validate($agents, new AgentArray());

        $this->buildViolation('Agent %number%: %message%')
            ->setParameter('%number%', '1')
            ->setParameter('%message%', 'This value should not be blank.')
            ->buildNextViolation('Agent %number%: %message%')
            ->setParameter('%number%', '2')
            ->setParameter('%message%', 'This value should not be blank.')
            ->assertRaised();
    }
}

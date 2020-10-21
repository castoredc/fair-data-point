<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function is_array;

class AgentArrayValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (! $constraint instanceof AgentArray) {
            throw new UnexpectedTypeException($constraint, AgentArray::class);
        }

        if ($value === null || $value === []) {
            return;
        }

        if (! is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        foreach ($value as $index => $agent) {
            if (! isset($agent['type'])) {
                $this->context->buildViolation($constraint->noTypeMessage)->addViolation();
            } else {
                if ($agent['type'] === Organization::TYPE) {
                    $collection = new Assert\Collection(
                        [
                            'name' => [
                                new Assert\NotBlank(),
                                new Assert\Type(['type' => 'string']),
                            ],
                            'country' => [
                                new Assert\NotBlank(),
                                new Assert\Country(),
                            ],
                            'city' => [
                                new Assert\NotBlank(),
                                new Assert\Type(['type' => 'string']),
                            ],
                            'department' => [
                                new Assert\Type(['type' => 'string']),
                            ],
                            'additionalInformation' => [
                                new Assert\Type(['type' => 'string']),
                            ],
                            'coordinatesLatitude' => [
                                new Assert\Type(['type' => 'string']),
                            ],
                            'coordinatesLongitude' => [
                                new Assert\Type(['type' => 'string']),
                            ],
                        ]
                    );

                    $this->mergeViolations($index, $agent, $collection, $constraint);
                } elseif ($agent['type'] === Person::TYPE) {
                    $collection = new Assert\Collection(
                        [
                            'firstName' => [
                                new Assert\NotBlank(),
                                new Assert\Type(['type' => 'string']),
                            ],
                            'middleName' => [
                                new Assert\NotBlank(),
                            ],
                            'lastName' => [
                                new Assert\NotBlank(),
                                new Assert\Type(['type' => 'string']),
                            ],
                            'email' => [
                                new Assert\NotBlank(),
                                new Assert\Email(),
                            ],
                            'orcid' => [
                                new Assert\Type(['type' => 'string']),
                            ],
                        ]
                    );

                    $this->mergeViolations($index, $agent, $collection, $constraint);
                } else {
                    $this->context->buildViolation($constraint->invalidTypeMessage)->addViolation();
                }
            }
        }
    }

    /**
     * @param mixed[] $agent
     */
    private function mergeViolations(int $index, array $agent, Assert\Collection $collection, AgentArray $constraint): void
    {
        $violations = $this->context->getValidator()->validate($agent, $collection);

        if ($violations->count() <= 0) {
            return;
        }

        foreach ($violations as $violationInstance) {
            foreach ($violationInstance as $violation) {
                /** @var ConstraintViolation $violation */
                $this->context->buildViolation($constraint->validationError)
                    ->setParameter('%number%', (string) $index)
                    ->setParameter('%message%', (string) $violation->getMessage())
                              ->addViolation();
            }
        }
    }
}

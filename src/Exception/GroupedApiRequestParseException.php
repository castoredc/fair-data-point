<?php

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GroupedApiRequestParseException extends Exception
{
    /**
     * @var ConstraintViolationListInterface[]
     */
    private $violations = [];

    public function __construct(array $violations)
    {
        parent::__construct();

        $this->violations = $violations ?? [];
    }

    public function toArray(): array
    {
        $fields = [];

        foreach ($this->violations as $index => $violationInstance) {
            foreach($violationInstance as $violation) {
                /* @var ConstraintViolation $violation */
                $fields[$index][$violation->getPropertyPath()][] = $violation->getMessage();
            }
        }

        return [
            'error' => 'Failed to parse API request.',
            'fields' => $fields,
        ];
    }
}

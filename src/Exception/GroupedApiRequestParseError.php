<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GroupedApiRequestParseError extends Exception
{
    /** @param array<ConstraintViolationListInterface> $violations */
    public function __construct(private array $violations = [])
    {
        parent::__construct();
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $fields = [];

        foreach ($this->violations as $index => $violationInstance) {
            foreach ($violationInstance as $violation) {
                /** @var ConstraintViolation $violation */
                $fields[$index][$violation->getPropertyPath()][] = $violation->getMessage();
            }
        }

        return [
            'error' => 'Failed to parse API request.',
            'fields' => $fields,
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiRequestParseError extends Exception
{
    /** @var ConstraintViolationListInterface */
    private $violations = [];

    public function __construct(?ConstraintViolationListInterface $violations)
    {
        parent::__construct();

        $this->violations = $violations ?? [];
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $fields = [];

        foreach ($this->violations as $violation) {
            /** @var ConstraintViolation $violation */
            $fields[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return [
            'error' => 'Failed to parse API request.',
            'fields' => $fields,
        ];
    }
}

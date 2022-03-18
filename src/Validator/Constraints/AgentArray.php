<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class AgentArray extends Constraint
{
    public string $message = 'This list does not contain valid agents.';

    public string $noTypeMessage = 'Please specify the type of agent.';

    public string $invalidTypeMessage = 'This agent is of an invalid type.';

    public string $validationError = 'Agent %number%: %message%';
}

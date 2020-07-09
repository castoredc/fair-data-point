<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AgentArray extends Constraint
{
    /** @var string */
    public $message = 'This list does not contain valid agents.';

    /** @var string */
    public $noTypeMessage = 'Please specify the type of agent.';

    /** @var string */
    public $invalidTypeMessage = 'This agent is of an invalid type.';

    /** @var string */
    public $validationError = 'Agent %number%: %message%';
}

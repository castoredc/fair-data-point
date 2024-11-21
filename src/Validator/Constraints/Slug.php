<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Slug extends Constraint
{
    public string $message = 'This slug is already in use, please use a different slug';
    public string $forbiddenSlug = 'This slug cannot be used, please use a different slug';

    /**
     * @param class-string<object> $type
     * @param string[]             $groups  An array of validation groups
     * @param mixed                $payload Domain-specific data attached to a constraint
     */
    #[HasNamedArguments]
    public function __construct(
        public $type,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }

    /** @return class-string<object> */
    public function getType()
    {
        return $this->type;
    }
}

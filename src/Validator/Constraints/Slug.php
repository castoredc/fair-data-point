<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use function array_values;

/** @Annotation */
class Slug extends Constraint
{
    public string $message = 'This slug is already in use, please use a different slug';
    public string $forbiddenSlug = 'This slug cannot be used, please use a different slug';

    /** @var class-string<object> */
    public $type;

    /** @param class-string<object> $type */
    public function __construct(
        $type,
        ?array $groups = null,
        $payload = null
    ) {
        $this->type = $type;
        parent::__construct([], $groups, $payload);
    }

    /** @return class-string<object> */
    public function getType()
    {
        /** @phpstan-ignore-next-line */
        return array_values($this->type)[0];
    }
}

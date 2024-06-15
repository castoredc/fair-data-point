<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Api\Request\ApiRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function assert;
use function count;
use function in_array;

class SlugValidator extends ConstraintValidator
{
    public const FORBIDDEN_SLUGS = [
        'add',
        'delete',
        'remove',
        'import',
        'catalog',
        'dataset',
        'distribution',
        'study',
    ];

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof Slug) {
            throw new UnexpectedTypeException($constraint, Slug::class);
        }

        if (in_array($value, self::FORBIDDEN_SLUGS, true)) {
            $this->context->buildViolation($constraint->forbiddenSlug)
                ->addViolation();
        }

        $request = $this->context->getObject();
        assert($request instanceof ApiRequest);
        $context = $request->getContext();

        /** @phpstan-ignore-next-line */
        if ($context !== null && $context->getSlug() === $value) {
            return;
        }

        $repository = $this->em->getRepository($constraint->getType());
        $foundEntities = $repository->findBy(['slug' => $value]);

        if (count($foundEntities) === 0) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}

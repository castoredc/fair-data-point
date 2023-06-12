<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Api\Request\ApiRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (! $constraint instanceof Slug) {
            throw new UnexpectedTypeException($constraint, Slug::class);
        }

        if(in_array($value, self::FORBIDDEN_SLUGS)) {
            $this->context->buildViolation($constraint->forbiddenSlug)
                ->addViolation();
        }

        /** @var ApiRequest $request */
        $request = $this->context->getObject();
        $context = $request->getContext();

        if($context !== null && $context->getSlug() === $value) {
            return;
        }

        $repository = $this->em->getRepository($constraint->getType());

        if($repository->findOneBy(['slug' => $value])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

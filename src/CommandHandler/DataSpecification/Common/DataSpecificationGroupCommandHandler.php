<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\Common;

use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\Common\Dependency\DependencyRule;
use App\Entity\DataSpecification\Common\Element;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use function assert;
use function class_exists;

abstract class DataSpecificationGroupCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    protected function parseDependencies(DependencyGroup $group, string $className): void
    {
        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DependencyGroup) {
                $this->parseDependencies($rule, $className);
            } elseif ($rule instanceof DependencyRule) {
                assert(class_exists($className));
                $node = $this->em->getRepository($className)->find($rule->getElementId());

                if ($node === null) {
                    throw new NotFound();
                }

                assert($node instanceof Element);

                $rule->setElement($node);
            }
        }
    }
}

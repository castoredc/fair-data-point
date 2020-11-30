<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Entity\Data\DataSpecification\Dependency\DependencyGroup;
use App\Entity\Data\DataSpecification\Dependency\DependencyRule;
use App\Entity\Data\DataSpecification\Element;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;
use function class_exists;

abstract class DataSpecificationGroupCommandHandler
{
    protected EntityManagerInterface $em;

    protected Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
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

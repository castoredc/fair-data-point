<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\Common;

use App\Entity\DataSpecification\Common\Dependency\DependencyGroup;
use App\Entity\DataSpecification\Common\Dependency\DependencyRule;
use App\Exception\InvalidEntityType;
use App\Service\VersionNumberHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

abstract class DataSpecificationVersionCommandHandler
{
    protected EntityManagerInterface $em;
    protected Security $security;
    protected VersionNumberHelper $versionNumberHelper;

    public function __construct(EntityManagerInterface $em, Security $security, VersionNumberHelper $versionNumberHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->versionNumberHelper = $versionNumberHelper;
    }

    /** @throws InvalidEntityType */
    protected function duplicateDependencies(DependencyGroup $group, ArrayCollection $elements): DependencyGroup
    {
        $newGroup = new DependencyGroup($group->getCombinator());

        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DependencyGroup) {
                $newRule = $this->duplicateDependencies($rule, $elements);
            } elseif ($rule instanceof DependencyRule) {
                $newRule = new DependencyRule($rule->getOperator(), $rule->getValue());
                $newRule->setElement($elements->get($rule->getElement()->getId()));
            } else {
                throw new InvalidEntityType();
            }

            $newGroup->addRule($newRule);
            $newRule->setGroup($newGroup);
        }

        return $newGroup;
    }
}

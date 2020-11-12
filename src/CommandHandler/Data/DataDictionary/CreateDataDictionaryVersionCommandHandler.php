<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataDictionary;

use App\Command\Data\DataDictionary\CreateDataDictionaryVersionCommand;
use App\Entity\Data\DataDictionary\DataDictionaryGroup;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyGroup;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyRule;
use App\Entity\Enum\VersionType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Service\VersionNumberHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataDictionaryVersionCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private Security $security;
    private VersionNumberHelper $versionNumberHelper;

    public function __construct(EntityManagerInterface $em, Security $security, VersionNumberHelper $versionNumberHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->versionNumberHelper = $versionNumberHelper;
    }

    public function __invoke(CreateDataDictionaryVersionCommand $command): DataDictionaryVersion
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataDictionary = $command->getDataDictionary();
        $latestVersion = $dataDictionary->getLatestVersion();

        $newVersion = $this->duplicateVersion($latestVersion, $command->getVersionType());

        $dataDictionary->addVersion($newVersion);

        $this->em->persist($newVersion);
        $this->em->persist($dataDictionary);

        $this->em->flush();

        return $newVersion;
    }

    private function duplicateVersion(DataDictionaryVersion $latestVersion, VersionType $versionType): DataDictionaryVersion
    {
        $versionNumber = $this->versionNumberHelper->getNewVersion($latestVersion->getVersion(), $versionType);

        $newVersion = new DataDictionaryVersion($versionNumber);

        // Add groups
        $groups = new ArrayCollection();
        $variables = new ArrayCollection();

        foreach ($latestVersion->getGroups() as $group) {
            /** @var DataDictionaryGroup $group */
            $newGroup = new DataDictionaryGroup($group->getTitle(), $group->getOrder(), $group->isRepeated(), $group->isDependent(), $newVersion);

            // TODO: Add variables
//            foreach ($group->getVariables() as $variable) {
//                /** @var Variable $variable */
//                $newTriple = new Triple(
//                    $newGroup,
//                    $nodes->get($triple->getSubject()->getId()),
//                    $predicates->get($triple->getPredicate()->getId()),
//                    $nodes->get($triple->getObject()->getId())
//                );
//
//                $newGroup->addTriple($newTriple);
//            }

            if ($group->isDependent() && $group->getDependencies() !== null) {
                $dependency = $group->getDependencies();
                $newDependency = $this->duplicateDependencies($dependency, $variables);

                $newGroup->setDependencies($newDependency);
            }

            $groups->add($newGroup);
        }

        $newVersion->setGroups($groups);

        return $newVersion;
    }

    /**
     * @throws InvalidEntityType
     */
    private function duplicateDependencies(DataDictionaryDependencyGroup $group, ArrayCollection $variables): DataDictionaryDependencyGroup
    {
        $newGroup = new DataDictionaryDependencyGroup($group->getCombinator());

        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DataDictionaryDependencyGroup) {
                $newRule = $this->duplicateDependencies($rule, $variables);
            } elseif ($rule instanceof DataDictionaryDependencyRule) {
                $newRule = new DataDictionaryDependencyRule($rule->getOperator(), $rule->getValue());
                $newRule->setVariable($variables->get($rule->getVariable()->getId()));
            } else {
                throw new InvalidEntityType();
            }

            $newGroup->addRule($newRule);
            $newRule->setGroup($newGroup);
        }

        return $newGroup;
    }
}

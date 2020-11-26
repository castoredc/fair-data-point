<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\CreateDataModelModuleMappingCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataSpecification\Mapping\GroupMapping;
use App\Entity\Data\DataSpecification\Mapping\Mapping;
use App\Entity\Enum\CastorEntityType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateDataModelModuleMappingCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    private CastorEntityHelper $entityHelper;

    public function __construct(EntityManagerInterface $em, Security $security, CastorEntityHelper $entityHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->entityHelper = $entityHelper;
    }

    /**
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws InvalidEntityType
     * @throws UserNotACastorUser
     */
    public function __invoke(CreateDataModelModuleMappingCommand $command): Mapping
    {
        $contents = $command->getDistribution();
        $distribution = $command->getDistribution()->getDistribution();
        $study = $distribution->getStudy();
        $dataModelVersion = $command->getDataModelVersion();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->entityHelper->useUser($user->getCastorUser());

        assert($study instanceof CastorStudy);

        $module = $this->em->getRepository(DataModelGroup::class)->find($command->getModule());

        if ($module === null || ! $module->isRepeated()) {
            throw new NotFound();
        }

        $element = $this->entityHelper->getEntityByTypeAndId($study, CastorEntityType::fromString($command->getStructureType()->toString()), $command->getElement());

        if ($study->getMappingByModuleAndVersion($module, $dataModelVersion) !== null) {
            $mapping = $study->getMappingByModuleAndVersion($module, $dataModelVersion);
            $mapping->setEntity($element);
        } else {
            $mapping = new GroupMapping($study, $module, $element, $dataModelVersion);
        }

        $this->em->persist($element);
        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}

<?php
declare(strict_types=1);

namespace App\CommandHandler\Castor;

use App\Command\Castor\GetCastorEntityCommand;
use App\Entity\Castor\CastorEntity;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;
use function assert;

#[AsMessageHandler]
class GetCastorEntityCommandHandler
{
    private EntityManagerInterface $em;
    private CastorEntityHelper $entityHelper;
    private Security $security;

    public function __construct(EntityManagerInterface $em, CastorEntityHelper $entityHelper, Security $security)
    {
        $this->em = $em;
        $this->entityHelper = $entityHelper;
        $this->security = $security;
    }

    /**
     * @throws NoAccessPermissionToStudy
     * @throws UserNotACastorUser
     * @throws InvalidEntityType
     */
    public function __invoke(GetCastorEntityCommand $command): CastorEntity
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->entityHelper->useUser($user->getCastorUser());

        $entity = $this->entityHelper->getEntityByTypeAndId($command->getStudy(), $command->getType(), $command->getId(), $command->getParentId());

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}

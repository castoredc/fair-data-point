<?php
declare(strict_types=1);

namespace App\CommandHandler\Security;

use App\Command\Security\AddPermissionToEntityCommand;
use App\Exception\NoAccessPermission;
use App\Exception\PermissionTypeNotSupported;
use App\Exception\UserAlreadyExists;
use App\Exception\UserNotFound;
use App\Security\Permission;
use App\Security\Providers\Castor\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddPermissionToEntityCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @throws UserNotFound
     * @throws NoAccessPermission
     * @throws PermissionTypeNotSupported
     * @throws UserAlreadyExists
     */
    public function __invoke(AddPermissionToEntityCommand $command): Permission
    {
        $entity = $command->getEntity();

        if (! $this->security->isGranted('manage', $entity)) {
            throw new NoAccessPermission();
        }

        if (! Permission::entitySupportsPermission($entity, $command->getType())) {
            throw new PermissionTypeNotSupported();
        }

        $repository = $this->em->getRepository(CastorUser::class);
        $user = $repository->findUserByEmail($command->getEmail())->getUser();

        if ($entity->getPermissionsForUser($user) !== null) {
            throw new UserAlreadyExists();
        }

        $permission = $entity->addPermissionForUser($user, $command->getType());

        $this->em->persist($entity);
        $this->em->flush();

        return $permission;
    }
}

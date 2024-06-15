<?php
declare(strict_types=1);

namespace App\CommandHandler\Security;

use App\Command\Security\EditPermissionToEntityCommand;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotFound;
use App\Security\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditPermissionToEntityCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /**
     * @throws UserNotFound
     * @throws NoAccessPermission
     */
    public function __invoke(EditPermissionToEntityCommand $command): Permission
    {
        $entity = $command->getEntity();

        if (! $this->security->isGranted('manage', $entity)) {
            throw new NoAccessPermission();
        }

        $user = $command->getUser();

        $permission = $entity->getPermissionsForUser($user);

        if ($permission === null) {
            throw new UserNotFound();
        }

        $permission->setType($command->getType());

        $this->em->persist($permission);
        $this->em->persist($entity);
        $this->em->flush();

        return $permission;
    }
}

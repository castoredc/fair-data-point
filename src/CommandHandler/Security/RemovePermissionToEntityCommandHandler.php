<?php
declare(strict_types=1);

namespace App\CommandHandler\Security;

use App\Command\Security\RemovePermissionToEntityCommand;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemovePermissionToEntityCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @throws NoAccessPermission */
    public function __invoke(RemovePermissionToEntityCommand $command): void
    {
        $entity = $command->getEntity();

        if (! $this->security->isGranted('manage', $entity)) {
            throw new NoAccessPermission();
        }

        $user = $command->getUser();

        if ($entity->getPermissionsForUser($user) === null) {
            throw new UserNotFound();
        }

        $entity->removePermissionForUser($user);

        $this->em->persist($entity);
        $this->em->flush();
    }
}

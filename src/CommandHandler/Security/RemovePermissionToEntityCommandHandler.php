<?php
declare(strict_types=1);

namespace App\CommandHandler\Security;

use App\Command\Security\RemovePermissionToEntityCommand;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class RemovePermissionToEntityCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
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

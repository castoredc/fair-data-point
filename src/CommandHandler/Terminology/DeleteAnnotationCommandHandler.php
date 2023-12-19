<?php
declare(strict_types=1);

namespace App\CommandHandler\Terminology;

use App\Command\Terminology\DeleteAnnotationCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;

#[AsMessageHandler]
class DeleteAnnotationCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(DeleteAnnotationCommand $command): void
    {
        $study = $command->getAnnotation()->getEntity()->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $this->em->remove($command->getAnnotation());
        $this->em->flush();
    }
}

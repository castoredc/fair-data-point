<?php
declare(strict_types=1);

namespace App\CommandHandler\Terminology;

use App\Command\Terminology\DeleteAnnotationCommand;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\StudyVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteAnnotationCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(DeleteAnnotationCommand $command): void
    {
        $study = $command->getAnnotation()->getEntity()->getStudy();

        if (! $this->security->isGranted(StudyVoter::EDIT, $study)) {
            throw new NoAccessPermission();
        }

        $this->em->remove($command->getAnnotation());
        $this->em->flush();
    }
}

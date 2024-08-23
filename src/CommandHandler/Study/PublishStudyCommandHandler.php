<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\PublishStudyCommand;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\StudyVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PublishStudyCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(PublishStudyCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted(StudyVoter::EDIT, $study)) {
            throw new NoAccessPermission();
        }

        $study->setIsPublished(true);
        $this->em->persist($study);

        $this->em->flush();
    }
}

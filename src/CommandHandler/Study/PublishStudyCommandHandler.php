<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\PublishStudyCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;

#[AsMessageHandler]
class PublishStudyCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(PublishStudyCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $study->setIsPublished(true);
        $this->em->persist($study);

        $this->em->flush();
    }
}

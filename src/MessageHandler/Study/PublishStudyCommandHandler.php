<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Exception\NoAccessPermission;
use App\Message\Study\PublishStudyCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class PublishStudyCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(PublishStudyCommand $message): void
    {
        $study = $message->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $study->setIsPublished(true);
        $this->em->persist($study);

        $this->em->flush();
    }
}

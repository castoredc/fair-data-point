<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Message\Study\PublishStudyCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PublishStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(PublishStudyCommand $message): void
    {
        $message->getStudy()->setIsPublished(true);
        $this->em->persist($message->getStudy());

        $this->em->flush();
    }
}

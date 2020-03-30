<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Message\Api\Study\UpdateConsentCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateConsentCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateConsentCommand $message): void
    {
        $message->getMetadata()->setConsentPublish($message->getPublish());
        $message->getMetadata()->setConsentSocialMedia($message->getSocialMedia());

        $this->em->persist($message->getMetadata());

        $this->em->flush();
    }
}

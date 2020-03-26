<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\ClearStudyContactsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearStudyContactsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(ClearStudyContactsCommand $message): void
    {
        $metadata = $message->getStudy()->getLatestMetadata();
        $metadata->setContacts([]);

        $this->em->persist($metadata);
        $this->em->flush();
    }
}

<?php
declare(strict_types=1);

namespace App\MessageHandler\Study\Provenance;

use App\Message\Study\Provenance\ClearStudyContactsCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearStudyContactsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(ClearStudyContactsCommand $message): void
    {
        $metadata = $message->getStudy()->getLatestMetadata();
        $metadata->setContacts(new ArrayCollection());

        $this->em->persist($metadata);
        $this->em->flush();
    }
}

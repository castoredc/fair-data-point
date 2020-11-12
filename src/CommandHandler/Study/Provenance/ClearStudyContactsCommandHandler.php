<?php
declare(strict_types=1);

namespace App\CommandHandler\Study\Provenance;

use App\Command\Study\Provenance\ClearStudyContactsCommand;
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

    public function __invoke(ClearStudyContactsCommand $command): void
    {
        $metadata = $command->getStudy()->getLatestMetadata();
        $metadata->setContacts(new ArrayCollection());

        $this->em->persist($metadata);
        $this->em->flush();
    }
}

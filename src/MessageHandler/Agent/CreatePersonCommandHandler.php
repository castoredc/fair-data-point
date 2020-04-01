<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use App\Message\Agent\CreatePersonCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreatePersonCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreatePersonCommand $message): void
    {
        $contact = new Person(
            $message->getFirstName(),
            $message->getMiddleName(),
            $message->getLastName(),
            $message->getEmail(),
            null,
            $message->getOrcid() !== null ? new Iri($message->getOrcid()) : null
        );

        $message->getStudy()->getLatestMetadata()->addContact($contact);

        $this->em->persist($contact);
        $this->em->persist($message->getStudy());

        $this->em->flush();
    }
}

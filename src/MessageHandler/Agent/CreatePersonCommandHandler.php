<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use App\Exception\NoAccessPermissionToStudy;
use App\Message\Agent\CreatePersonCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreatePersonCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreatePersonCommand $message): void
    {
        if (! $this->security->isGranted('edit', $message->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

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

<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\CreatePersonCommand;
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
        /** @var Study|null $study */
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

        if ($study === null) {
            throw new StudyNotFoundException();
        }

        $contact = new Person(
            $message->getFirstName(),
            $message->getMiddleName(),
            $message->getLastName(),
            $message->getEmail(),
            null,
            new Iri($message->getOrcid())
        );

        $study->getLatestMetadata()->addContact($contact);

        $this->em->persist($contact);
        $this->em->persist($study);

        $this->em->flush();
    }
}

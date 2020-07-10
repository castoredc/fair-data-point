<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\PersonAlreadyExists;
use App\Message\Agent\AddStudyContactCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddStudyContactCommandHandler implements MessageHandlerInterface
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

    public function __invoke(AddStudyContactCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $repository = $this->em->getRepository(Person::class);

        if ($command->getId() !== null) {
            /** @var Person|null $contact */
            $contact = $repository->find($command->getId());

            if ($contact === null) {
                throw new NotFound();
            }
        } else {
            if ($repository->findOneBy(['email' => $command->getEmail()]) !== null) {
                throw new PersonAlreadyExists();
            }

            $contact = new Person(
                $command->getFirstName(),
                $command->getMiddleName(),
                $command->getLastName(),
                $command->getEmail(),
                null,
                $command->getOrcid() !== null ? new Iri($command->getOrcid()) : null
            );
        }

        $command->getStudy()->getLatestMetadata()->addContact($contact);

        $this->em->persist($contact);
        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

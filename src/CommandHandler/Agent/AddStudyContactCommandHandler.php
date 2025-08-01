<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\AddStudyContactCommand;
use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\Iri;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\PersonAlreadyExists;
use App\Security\Authorization\Voter\StudyVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddStudyContactCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(AddStudyContactCommand $command): void
    {
        if (! $this->security->isGranted(StudyVoter::EDIT, $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $repository = $this->em->getRepository(Person::class);

        if ($command->getId() !== null) {
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
                $command->getOrcid() !== null ? new Iri($command->getOrcid()) : null,
                NameOrigin::peer()
            );
        }

        $command->getStudy()->getLatestMetadata()->addStudyTeamMember($contact, true);

        $this->em->persist($contact);
        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

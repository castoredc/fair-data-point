<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\RemoveStudyContactCommand;
use App\Entity\FAIRData\Agent\Person;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;

#[AsMessageHandler]
class RemoveStudyContactCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(RemoveStudyContactCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $repository = $this->em->getRepository(Person::class);

        $contact = $repository->find($command->getId());

        if ($contact === null) {
            throw new NotFound();
        }

        foreach ($command->getStudy()->getLatestMetadata()->getStudyTeam() as $studyTeamMember) {
            if ($studyTeamMember->getPerson() !== $contact) {
                continue;
            }

            $command->getStudy()->getLatestMetadata()->removeStudyTeamMember($studyTeamMember);
            $this->em->remove($studyTeamMember);
        }

        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

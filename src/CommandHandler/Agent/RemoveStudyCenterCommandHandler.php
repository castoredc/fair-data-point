<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\RemoveStudyCenterCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveStudyCenterCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(RemoveStudyCenterCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $organizationRepository = $this->em->getRepository(Organization::class);

        $organization = $organizationRepository->find($command->getId());

        if ($organization === null) {
            throw new NotFound();
        }

        foreach ($command->getStudy()->getLatestMetadata()->getCenters() as $participatingCenter) {
            if ($participatingCenter->getOrganization() !== $organization) {
                continue;
            }

            $command->getStudy()->getLatestMetadata()->removeParticipatingCenter($participatingCenter);
            $this->em->remove($participatingCenter);
        }

        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

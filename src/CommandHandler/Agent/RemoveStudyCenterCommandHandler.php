<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\RemoveStudyCenterCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class RemoveStudyCenterCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(RemoveStudyCenterCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $repository = $this->em->getRepository(Organization::class);

        $organization = $repository->find($command->getId());
        assert($organization instanceof Organization || $organization === null);

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

<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\AddStudyCenterCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class AddStudyCenterCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(AddStudyCenterCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $organizationRepository = $this->em->getRepository(Organization::class);

        $organization = $organizationRepository->find($command->getOrganizationId());

        if ($organization === null) {
            throw new NotFound();
        }

        assert($organization instanceof Organization);

        $command->getStudy()->getLatestMetadata()->addCenter($organization);

        $this->em->persist($organization);
        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

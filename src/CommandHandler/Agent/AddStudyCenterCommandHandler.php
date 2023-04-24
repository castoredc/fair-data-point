<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\AddStudyCenterCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddStudyCenterCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(AddStudyCenterCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $repository = $this->em->getRepository(Organization::class);

        $organization = $repository->find($command->getOrganizationId());

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

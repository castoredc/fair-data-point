<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\CreateStudyCenterCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Exception\CountryNotFound;
use App\Exception\NoAccessPermissionToStudy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;
use function assert;

#[AsMessageHandler]
class CreateStudyCenterCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateStudyCenterCommand $command): void
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $country = $this->em->getRepository(Country::class)->find($command->getCountry());
        assert($country instanceof Country || $country === null);

        if ($country === null) {
            throw new CountryNotFound();
        }

        $organization = new Organization(
            null,
            $command->getName(),
            null,
            $country->getCode(),
            $command->getCity(),
            null,
            null,
        );

        $organization->setCountry($country);

        $this->em->persist($organization);

        $command->getStudy()->getLatestMetadata()->addCenter($organization);

        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

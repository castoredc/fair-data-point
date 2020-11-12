<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\CreateDepartmentAndOrganizationCommand;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Exception\CountryNotFound;
use App\Exception\NoAccessPermissionToStudy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateDepartmentAndOrganizationCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateDepartmentAndOrganizationCommand $command): void
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
            $command->getOrganizationSlug(),
            $command->getName(),
            $command->getHomepage(),
            $country->getCode(),
            $command->getCity(),
            $command->getCoordinatesLatitude(),
            $command->getCoordinatesLongitude()
        );

        $organization->setCountry($country);

        $this->em->persist($organization);

        $department = new Department(
            $command->getDepartmentSlug(),
            $command->getDepartment(),
            $organization,
            $command->getAdditionalInformation()
        );

        $command->getStudy()->getLatestMetadata()->addCenter($department);

        $this->em->persist($department);
        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}

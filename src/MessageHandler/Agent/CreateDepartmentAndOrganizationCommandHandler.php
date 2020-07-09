<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Exception\CountryNotFound;
use App\Exception\NoAccessPermissionToStudy;
use App\Message\Agent\CreateDepartmentAndOrganizationCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDepartmentAndOrganizationCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateDepartmentAndOrganizationCommand $message): void
    {
        if (! $this->security->isGranted('edit', $message->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        /** @var Country|null $country */
        $country = $this->em->getRepository(Country::class)->find($message->getCountry());

        if ($country === null) {
            throw new CountryNotFound();
        }

        $organization = new Organization(
            $message->getOrganizationSlug(),
            $message->getName(),
            $message->getHomepage(),
            $country->getCode(),
            $message->getCity(),
            $message->getCoordinatesLatitude(),
            $message->getCoordinatesLongitude()
        );

        $organization->setCountry($country);

        $this->em->persist($organization);

        $department = new Department(
            $message->getDepartmentSlug(),
            $message->getDepartment(),
            $organization,
            $message->getAdditionalInformation()
        );

        $message->getStudy()->getLatestMetadata()->addCenter($department);

        $this->em->persist($department);
        $this->em->persist($message->getStudy());

        $this->em->flush();
    }
}

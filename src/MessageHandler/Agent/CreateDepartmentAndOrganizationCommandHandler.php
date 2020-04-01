<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Exception\CountryNotFound;
use App\Message\Agent\CreateDepartmentAndOrganizationCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDepartmentAndOrganizationCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateDepartmentAndOrganizationCommand $message): void
    {
        /** @var Country|null $country */
        $country = $this->em->getRepository(Country::class)->find($message->getCountry());

        if ($country === null) {
            throw new CountryNotFound();
        }

        $organization = new Organization(
            $message->getOrganizationSlug(),
            $message->getName(),
            $message->getHomepage(),
            $country,
            $message->getCity(),
            $message->getCoordinatesLatitude(),
            $message->getCoordinatesLongitude()
        );

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

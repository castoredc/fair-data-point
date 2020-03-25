<?php

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Exception\CountryNotFoundException;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\CreateDepartmentAndOrganizationCommand;
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

    public function __invoke(CreateDepartmentAndOrganizationCommand $message)
    {
        /** @var Study|null $study */
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

        /** @var Country|null $country */
        $country = $this->em->getRepository(Country::class)->find($message->getCountry());

        if(!$study)
        {
            throw new StudyNotFoundException();
        }
        if(!$country)
        {
            throw new CountryNotFoundException();
        }

        $organization = new Organization(
            $message->getOrganizationSlug(),
            $message->getName(),
            $message->getHomepage(),
            $country,
            $message->getCity()
        );

        $this->em->persist($organization);

        $department = new Department(
            $message->getDepartmentSlug(),
            $message->getDepartment(),
            $organization,
            $message->getAdditionalInformation()
        );

        $study->getLatestMetadata()->addCenter($department);

        $this->em->persist($department);
        $this->em->persist($study);

        $this->em->flush();
    }
}
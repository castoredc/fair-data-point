<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\CreateAffiliationCommand;
use App\Entity\FAIRData\Agent\Affiliation;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Model\Grid\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class CreateAffiliationCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security, private ApiClient $gridApiClient)
    {
    }

    public function __invoke(CreateAffiliationCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_USER')) {
            throw new NoAccessPermission();
        }

        $person = $command->getPerson();

        if ($command->getOrganizationSource()->isDatabase()) {
            $organization = $this->em->getRepository(Organization::class)->find($command->getOrganizationId());

            if ($organization === null) {
                throw new NotFound();
            }

            assert($organization instanceof Organization);
        } elseif ($command->getOrganizationSource()->isGrid()) {
            try {
                $gridInstitute = $this->gridApiClient->getInstituteById($command->getOrganizationId());
            } catch (NotFound) {
                throw new NotFound();
            }

            $mainAddress = $gridInstitute->getMainAddress();

            $country = $this->em->getRepository(Country::class)->find($mainAddress->getCountryCode());
            assert($country instanceof Country);

            $organization = new Organization(
                null,
                $gridInstitute->getName(),
                $gridInstitute->hasLinks() ? $gridInstitute->getLinks()[0] : null,
                $mainAddress->getCity(),
                (string) $mainAddress->getLat(),
                (string) $mainAddress->getLng(),
                $mainAddress->getCountryCode()
            );

            $organization->setCountry($country);
        } else {
            $country = $this->em->getRepository(Country::class)->find($command->getOrganizationCountry());
            assert($country instanceof Country);

            $organization = new Organization(
                null,
                $command->getOrganizationName(),
                null,
                $command->getOrganizationCity(),
                null,
                null,
                $command->getOrganizationCountry()
            );

            $organization->setCountry($country);
        }

        if ($command->getDepartmentSource()->isDatabase()) {
            $department = $this->em->getRepository(Department::class)->find($command->getDepartmentId());

            if ($department === null) {
                throw new NotFound();
            }
        } else {
            $department = new Department(null, $command->getDepartmentName(), $organization, null);
        }

        $affiliation = new Affiliation($person, $organization, $department, $command->getPosition());
        $person->addAffiliation($affiliation);

        $this->em->persist($person);
        $this->em->persist($organization);
        $this->em->persist($department);
        $this->em->flush();
    }
}

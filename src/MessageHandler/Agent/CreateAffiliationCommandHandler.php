<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Agent\Affiliation;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Message\Agent\CreateAffiliationCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateAffiliationCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateAffiliationCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_USER')) {
            throw new NoAccessPermission();
        }

        $person = $command->getPerson();

        if ($command->getOrganizationSource()->isDatabase()) {
            $organization = $this->em->getRepository(Organization::class)->find($command->getOrganizationId());
            assert($organization instanceof Organization || $organization === null);

            if ($organization === null) {
                throw new NotFound();
            }
        } else {
            $country = $this->em->getRepository(Country::class)->find($command->getOrganizationCountry());

            $organization = new Organization(
                null,
                $command->getOrganizationName(),
                null,
                $command->getOrganizationCountry(),
                $command->getOrganizationCity(),
                null,
                null
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

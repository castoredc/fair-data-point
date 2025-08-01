<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\FindOrganizationsCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Entity\Grid\Institute;
use App\Exception\CountryNotFound;
use App\Exception\ErrorFetchingGridData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Model\Grid\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function in_array;

#[AsMessageHandler]
class FindOrganizationsCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security, private ApiClient $gridApiClient)
    {
    }

    /** @return array<Institute|Organization> */
    public function __invoke(FindOrganizationsCommand $command): array
    {
        if (! $this->security->isGranted('ROLE_USER')) {
            throw new NoAccessPermission();
        }

        $country = $this->em->getRepository(Country::class)->find($command->getCountry());
        assert($country instanceof Country || $country === null);

        if ($country === null) {
            throw new CountryNotFound();
        }

        $repository = $this->em->getRepository(Organization::class);

        /** @var Organization[] $dbOrganizations */
        $dbOrganizations = $repository->findOrganizations($country, $command->getSearch());

        $gridIds = [];

        foreach ($dbOrganizations as $dbOrganization) {
            if ($dbOrganization->getGridId() === null) {
                continue;
            }

            $gridIds[] = $dbOrganization->getGridId();
        }

        $organizations = $dbOrganizations;

        try {
            $gridInstitutes = $this->gridApiClient->findInstitutesByNameAndCountry($command->getSearch(), $command->getCountry());
        } catch (ErrorFetchingGridData | NotFound) {
            $gridInstitutes = [];
        }

        foreach ($gridInstitutes as $gridInstitute) {
            if (in_array($gridInstitute->getId(), $gridIds, true)) {
                continue;
            }

            $organizations[] = $gridInstitute;
        }

        return $organizations;
    }
}

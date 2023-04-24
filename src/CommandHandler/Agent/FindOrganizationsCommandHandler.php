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
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;
use function in_array;

class FindOrganizationsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    private ApiClient $gridApiClient;

    public function __construct(EntityManagerInterface $em, Security $security, ApiClient $gridApiClient)
    {
        $this->em = $em;
        $this->security = $security;
        $this->gridApiClient = $gridApiClient;
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
        } catch (ErrorFetchingGridData | NotFound $e) {
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

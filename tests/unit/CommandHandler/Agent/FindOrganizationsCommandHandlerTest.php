<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Agent;

use App\Command\Agent\FindOrganizationsCommand;
use App\CommandHandler\Agent\FindOrganizationsCommandHandler;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Entity\Grid\Institute;
use App\Exception\CountryNotFound;
use App\Exception\ErrorFetchingGridData;
use App\Exception\NoAccessPermission;
use App\Model\Grid\ApiClient;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class FindOrganizationsCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserIsNotAuthenticated(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_USER')->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('getRepository');

        $handler = new FindOrganizationsCommandHandler($em, $security, $this->createMock(ApiClient::class));

        $this->expectException(NoAccessPermission::class);
        $handler(new FindOrganizationsCommand('NL', 'hospital'));
    }

    public function testThrowsWhenCountryNotFound(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->with(Country::class)->willReturn($this->countryRepository(null));

        $handler = new FindOrganizationsCommandHandler($em, $this->grantedSecurity(), $this->createMock(ApiClient::class));

        $this->expectException(CountryNotFound::class);
        $handler(new FindOrganizationsCommand('NL', 'hospital'));
    }

    public function testMergesDatabaseAndGridResultsAndDeduplicatesByGridId(): void
    {
        $country = $this->country('NL');

        $dbWithGridId = $this->organization('Linked Hospital');
        $dbWithGridId->setGridId('grid-1');
        $dbWithoutGridId = $this->organization('Local Clinic');

        $duplicate = new Institute('grid-1', 'Linked Hospital');
        $newInstitute = new Institute('grid-2', 'Grid Research Center');

        $organizationRepository = $this->createMock(OrganizationRepository::class);
        $organizationRepository->expects(self::once())
            ->method('findOrganizations')
            ->with($country, 'hospital')
            ->willReturn([$dbWithGridId, $dbWithoutGridId]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Country::class, $this->countryRepository($country)],
            [Organization::class, $organizationRepository],
        ]);

        $gridClient = $this->createMock(ApiClient::class);
        $gridClient->expects(self::once())
            ->method('findInstitutesByNameAndCountry')
            ->with('hospital', 'NL')
            ->willReturn([$duplicate, $newInstitute]);

        $handler = new FindOrganizationsCommandHandler($em, $this->grantedSecurity(), $gridClient);
        $result = $handler(new FindOrganizationsCommand('NL', 'hospital'));

        // Both DB organizations plus only the non-duplicate grid institute.
        self::assertSame([$dbWithGridId, $dbWithoutGridId, $newInstitute], $result);
    }

    public function testReturnsOnlyDatabaseResultsWhenGridLookupFails(): void
    {
        $country = $this->country('NL');
        $dbOrganization = $this->organization('Local Clinic');

        $organizationRepository = $this->createMock(OrganizationRepository::class);
        $organizationRepository->method('findOrganizations')->willReturn([$dbOrganization]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Country::class, $this->countryRepository($country)],
            [Organization::class, $organizationRepository],
        ]);

        $gridClient = $this->createMock(ApiClient::class);
        $gridClient->method('findInstitutesByNameAndCountry')->willThrowException(new ErrorFetchingGridData());

        $handler = new FindOrganizationsCommandHandler($em, $this->grantedSecurity(), $gridClient);
        $result = $handler(new FindOrganizationsCommand('NL', 'hospital'));

        self::assertSame([$dbOrganization], $result);
    }

    private function country(string $code): Country
    {
        $country = new Country();
        $country->setCode($code);
        $country->setName('Country ' . $code);

        return $country;
    }

    private function organization(string $name): Organization
    {
        return new Organization('slug-' . $name, $name, null, 'Amsterdam', null, null);
    }

    private function grantedSecurity(): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_USER')->willReturn(true);

        return $security;
    }

    private function countryRepository(?Country $country): EntityRepository
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->willReturn($country);

        return $repository;
    }
}

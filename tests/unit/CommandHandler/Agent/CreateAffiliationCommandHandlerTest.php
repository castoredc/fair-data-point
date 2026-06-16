<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Agent;

use App\Command\Agent\CreateAffiliationCommand;
use App\CommandHandler\Agent\CreateAffiliationCommandHandler;
use App\Entity\Enum\DepartmentSource;
use App\Entity\Enum\NameOrigin;
use App\Entity\Enum\OrganizationSource;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Country;
use App\Entity\Grid\Address;
use App\Entity\Grid\Institute;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Model\Grid\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateAffiliationCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserIsNotAuthenticated(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_USER')->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = new CreateAffiliationCommandHandler($em, $security, $this->createMock(ApiClient::class));

        $this->expectException(NoAccessPermission::class);
        $handler($this->databaseCommand());
    }

    public function testCreatesAffiliationFromDatabaseOrganizationAndDepartment(): void
    {
        $person = $this->person();
        $organization = $this->organization();
        $department = new Department('cardiology', 'Cardiology', $organization, null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Organization::class, $this->repositoryReturning('org-1', $organization)],
            [Department::class, $this->repositoryReturning('dep-1', $department)],
        ]);

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $handler = new CreateAffiliationCommandHandler($em, $this->grantedSecurity(), $this->createMock(ApiClient::class));
        $handler($this->databaseCommand($person));

        self::assertCount(1, $person->getAffiliations());
        $affiliation = $person->getAffiliations()->first();
        self::assertSame($organization, $affiliation->getOrganization());
        self::assertSame($department, $affiliation->getDepartment());
        self::assertContains($person, $persisted);
        self::assertContains($organization, $persisted);
        self::assertContains($department, $persisted);
    }

    public function testThrowsNotFoundWhenDatabaseOrganizationMissing(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Organization::class, $this->repositoryReturning('org-1', null)],
        ]);
        $em->expects(self::never())->method('flush');

        $handler = new CreateAffiliationCommandHandler($em, $this->grantedSecurity(), $this->createMock(ApiClient::class));

        $this->expectException(NotFound::class);
        $handler($this->databaseCommand());
    }

    public function testThrowsNotFoundWhenDatabaseDepartmentMissing(): void
    {
        $organization = $this->organization();

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Organization::class, $this->repositoryReturning('org-1', $organization)],
            [Department::class, $this->repositoryReturning('dep-1', null)],
        ]);
        $em->expects(self::never())->method('flush');

        $handler = new CreateAffiliationCommandHandler($em, $this->grantedSecurity(), $this->createMock(ApiClient::class));

        $this->expectException(NotFound::class);
        $handler($this->databaseCommand());
    }

    public function testBuildsOrganizationFromGridInstitute(): void
    {
        $person = $this->person();
        $country = $this->country('GB');

        $institute = new Institute('grid-99', 'Grid University');
        $institute->addAddress(new Address(1, true, 'London', 'GB', 51.5, -0.12));
        $institute->addLink(new Iri('https://grid-university.example'));

        $gridClient = $this->createMock(ApiClient::class);
        $gridClient->expects(self::once())->method('getInstituteById')->with('grid-99')->willReturn($institute);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Country::class, $this->repositoryReturning('GB', $country)],
        ]);

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $command = new CreateAffiliationCommand(
            $person,
            OrganizationSource::grid(),
            'GB',
            DepartmentSource::manual(),
            'Researcher',
            'grid-99',
            null,
            null,
            null,
            'Department of Science',
        );

        $handler = new CreateAffiliationCommandHandler($em, $this->grantedSecurity(), $gridClient);
        $handler($command);

        $affiliation = $person->getAffiliations()->first();
        $organization = $affiliation->getOrganization();
        self::assertSame('Grid University', $organization->getName());
        self::assertSame($country, $organization->getCountry());
        // A manual department gets created and attached to the new organization.
        self::assertSame('Department of Science', $affiliation->getDepartment()->getName());
        self::assertContains($organization, $persisted);
    }

    public function testTranslatesGridNotFoundIntoNotFound(): void
    {
        $gridClient = $this->createMock(ApiClient::class);
        $gridClient->method('getInstituteById')->willThrowException(new NotFound());

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $command = new CreateAffiliationCommand(
            $this->person(),
            OrganizationSource::grid(),
            'GB',
            DepartmentSource::manual(),
            'Researcher',
            'grid-99',
            null,
            null,
            null,
            'Department of Science',
        );

        $handler = new CreateAffiliationCommandHandler($em, $this->grantedSecurity(), $gridClient);

        $this->expectException(NotFound::class);
        $handler($command);
    }

    public function testBuildsManualOrganizationAndManualDepartment(): void
    {
        $person = $this->person();
        $country = $this->country('NL');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Country::class, $this->repositoryReturning('NL', $country)],
        ]);

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $command = new CreateAffiliationCommand(
            $person,
            OrganizationSource::manual(),
            'NL',
            DepartmentSource::manual(),
            'Lead',
            null,
            'Manual Hospital',
            'Amsterdam',
            null,
            'Cardiology',
        );

        $handler = new CreateAffiliationCommandHandler($em, $this->grantedSecurity(), $this->createMock(ApiClient::class));
        $handler($command);

        $affiliation = $person->getAffiliations()->first();
        self::assertSame('Manual Hospital', $affiliation->getOrganization()->getName());
        self::assertSame($country, $affiliation->getOrganization()->getCountry());
        self::assertSame('Cardiology', $affiliation->getDepartment()->getName());
        self::assertSame('Lead', $affiliation->getPosition());
    }

    private function databaseCommand(?Person $person = null): CreateAffiliationCommand
    {
        return new CreateAffiliationCommand(
            $person ?? $this->person(),
            OrganizationSource::database(),
            'NL',
            DepartmentSource::database(),
            'Researcher',
            'org-1',
            null,
            null,
            'dep-1',
            null,
        );
    }

    private function person(): Person
    {
        $person = new Person('Ada', null, 'Lovelace', 'ada@example.com', null, null, NameOrigin::user());
        $person->setSlug('ada-lovelace');

        return $person;
    }

    private function organization(): Organization
    {
        return new Organization('example-hospital', 'Example Hospital', null, 'Amsterdam', null, null);
    }

    private function country(string $code): Country
    {
        $country = new Country();
        $country->setCode($code);
        $country->setName('Country ' . $code);

        return $country;
    }

    private function grantedSecurity(): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_USER')->willReturn(true);

        return $security;
    }

    private function repositoryReturning(string $id, ?object $result): EntityRepository
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($id)->willReturn($result);

        return $repository;
    }
}

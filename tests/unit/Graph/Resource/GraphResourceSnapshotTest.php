<?php
declare(strict_types=1);

namespace App\Tests\unit\Graph\Resource;

use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Country;
use App\Entity\Iri;
use App\Graph\Resource\Agent\Department\DepartmentGraphResource;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use App\Graph\Resource\CountryGraphResource;
use App\Tests\Helper\RdfSnapshotAssertions;
use PHPUnit\Framework\TestCase;

/**
 * Golden-file tests for the GraphResource rendering classes. The committed
 * fixtures in tests/fixtures/rdf/ guard the EasyRdf Turtle and JSON-LD
 * serialisation output across dependency upgrades.
 */
final class GraphResourceSnapshotTest extends TestCase
{
    use RdfSnapshotAssertions;

    private const BASE_URL = 'https://fdp.example.com';

    public function testPersonRendersAsFoafPerson(): void
    {
        $person = self::person();

        self::assertGraphMatchesSnapshots(
            (new PersonGraphResource($person, self::BASE_URL))->toGraph(),
            'graph-resource/person'
        );
    }

    public function testPersonWithOrcidUsesOrcidUrl(): void
    {
        $person = self::person(new Iri('0000-0002-1825-0097'));

        self::assertGraphMatchesSnapshots(
            (new PersonGraphResource($person, self::BASE_URL))->toGraph(),
            'graph-resource/person-with-orcid'
        );
    }

    public function testOrganizationRendersWithCountry(): void
    {
        $organization = self::organization();
        $organization->setCountry(self::country());

        self::assertGraphMatchesSnapshots(
            (new OrganizationGraphResource($organization, self::BASE_URL))->toGraph(),
            'graph-resource/organization'
        );
    }

    public function testOrganizationWithHomepageUsesHomepageUrl(): void
    {
        $organization = self::organization(new Iri('https://www.example-hospital.org'));

        self::assertGraphMatchesSnapshots(
            (new OrganizationGraphResource($organization, self::BASE_URL))->toGraph(),
            'graph-resource/organization-with-homepage'
        );
    }

    public function testDepartmentRendersItsOrganization(): void
    {
        $organization = self::organization();
        $organization->setCountry(self::country());

        $department = new Department('cardiology', 'Cardiology', $organization, null);

        self::assertGraphMatchesSnapshots(
            (new DepartmentGraphResource($department, self::BASE_URL))->toGraph(),
            'graph-resource/department'
        );
    }

    public function testCountryRendersAsDctermsLocation(): void
    {
        self::assertGraphMatchesSnapshots(
            (new CountryGraphResource(self::country(), self::BASE_URL))->toGraph(),
            'graph-resource/country'
        );
    }

    public function testRenderingTheSameGraphTwiceIsDeterministic(): void
    {
        $organization = self::organization();
        $organization->setCountry(self::country());

        $first = (new OrganizationGraphResource($organization, self::BASE_URL))->toGraph()->serialise('turtle');
        $second = (new OrganizationGraphResource($organization, self::BASE_URL))->toGraph()->serialise('turtle');

        self::assertSame($first, $second);
    }

    private static function person(?Iri $orcid = null): Person
    {
        $person = new Person('Ada', null, 'Lovelace', 'ada.lovelace@example.com', null, $orcid, NameOrigin::user());

        // The constructor generates a slug with a uniqid() suffix; fix it for determinism.
        $person->setSlug('ada-lovelace');

        return $person;
    }

    private static function organization(?Iri $homepage = null): Organization
    {
        return new Organization(
            'example-hospital',
            'Example Hospital',
            $homepage,
            'Amsterdam',
            '52.35000000',
            '4.91666667'
        );
    }

    private static function country(): Country
    {
        $country = new Country();
        $country->setCode('NL');
        $country->setName('Netherlands');
        $country->setAbbreviation('NLD');
        $country->setTld('.nl');
        $country->setCastorCountryId('155');

        return $country;
    }
}

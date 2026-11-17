<?php
declare(strict_types=1);

namespace App\Tests\integration\Factory;

use App\Entity\FAIRData\Catalog;
use App\Tests\Factory\CastorStudyFactory;
use App\Tests\Factory\CatalogFactory;
use App\Tests\Factory\DatasetPermissionFactory;
use App\Tests\Factory\DistributionFactory;
use App\Tests\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * Proves the Foundry factories can build and persist the core entity graph
 * against the migrated test database.
 */
final class FactorySmokeTest extends KernelTestCase
{
    use Factories;

    public function testUserWithPersonCanBePersisted(): void
    {
        $user = UserFactory::createOne();

        self::assertNotNull($user->getPerson());
        self::assertContains('ROLE_USER', $user->getRoles());
    }

    public function testCatalogCanBePersistedAndReloaded(): void
    {
        $catalog = CatalogFactory::createOne(['slug' => 'smoke-test-catalog']);

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->clear();

        $reloaded = $entityManager->getRepository(Catalog::class)->findOneBy(['slug' => 'smoke-test-catalog']);

        self::assertInstanceOf(Catalog::class, $reloaded);
        self::assertSame($catalog->getId(), $reloaded->getId());
    }

    public function testDistributionIsLinkedToItsDataset(): void
    {
        $distribution = DistributionFactory::new()->published()->create();

        self::assertTrue($distribution->isPublished());
        self::assertContains($distribution, $distribution->getDataset()->getDistributions()->toArray());
    }

    public function testDatasetPermissionGrantsAccessToTheUser(): void
    {
        $permission = DatasetPermissionFactory::createOne();

        $dataset = $permission->getEntity();
        $user = $permission->getUser();

        self::assertSame($permission, $dataset->getPermissionsForUser($user));
    }

    public function testCastorStudyCanBePersisted(): void
    {
        $study = CastorStudyFactory::createOne(['name' => 'Smoke Test Study']);

        self::assertSame('Smoke Test Study', $study->getName());
        self::assertNotNull($study->getSourceId());
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\integration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\Depends;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Proves the integration test harness works: the kernel boots in the test
 * environment, the test database is reachable and migrated, and
 * dama/doctrine-test-bundle rolls back every change after each test.
 */
final class TestDatabaseSmokeTest extends KernelTestCase
{
    public function testDatabaseStartsEmptyAndCanBeWrittenTo(): void
    {
        $connection = $this->getConnection();

        self::assertSame(0, (int) $connection->fetchOne('SELECT COUNT(*) FROM ontology'));

        $connection->executeStatement(
            'INSERT INTO ontology (id, url, name, bio_portal_id) VALUES (?, ?, ?, ?)',
            ['00000000-0000-0000-0000-000000000001', 'https://example.org/ontology', 'Smoke test ontology', 'SMOKE'],
        );

        self::assertSame(1, (int) $connection->fetchOne('SELECT COUNT(*) FROM ontology'));
    }

    #[Depends('testDatabaseStartsEmptyAndCanBeWrittenTo')]
    public function testChangesFromPreviousTestWereRolledBack(): void
    {
        self::assertSame(0, (int) $this->getConnection()->fetchOne('SELECT COUNT(*) FROM ontology'));
    }

    private function getConnection(): Connection
    {
        $connection = self::getContainer()->get(Connection::class);
        self::assertInstanceOf(Connection::class, $connection);

        return $connection;
    }
}

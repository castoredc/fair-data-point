<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\CreateRDFDistributionCommand;
use App\CommandHandler\Distribution\RDF\CreateRDFDistributionCommandHandler;
use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Entity\Iri;
use App\Entity\Version as VersionNumber;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\CastorServer;
use App\Security\User;
use App\Service\Distribution\MysqlBasedDistributionService;
use App\Service\Distribution\TripleStoreBasedDistributionService;
use App\Service\EncryptionService;
use App\Tests\Helper\DoctrineEntityIdSetter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateRDFDistributionCommandHandlerTest extends TestCase
{
    use DoctrineEntityIdSetter;

    private const ENCRYPTION_KEY = '0000000000000000000000000000000000000000000000000000000000000000';
    private const DATA_MODEL_ID = 'data-model-id';
    private const METADATA_MODEL_ID = 'metadata-model-id';
    private const LICENSE_ID = 'license-id';

    public function testCreatesMysqlBackedRdfDistributionAndProvisionsDatabaseViaMysqlService(): void
    {
        $dataset = $this->dataset();
        $command = $this->command($dataset);

        $metadataModel = new MetadataModel('Metadata model', null);
        $license = new License('cc-by', new Iri('https://example.com/license'), 'CC-BY');
        $dataModel = $this->dataModel();

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [MetadataModel::class, $this->repositoryReturning(self::METADATA_MODEL_ID, $metadataModel)],
            [License::class, $this->repositoryReturning(self::LICENSE_ID, $license)],
            [DataModel::class, $this->repositoryReturning(self::DATA_MODEL_ID, $dataModel)],
        ]);
        $this->assignDistributionIdOnPersist($em);

        // Two flush calls: one after the initial persist, one after database provisioning.
        $em->expects(self::exactly(2))->method('flush');
        $em->expects(self::never())->method('remove');

        $security = $this->grantedSecurity($dataset);

        $mysqlService = $this->createMock(MysqlBasedDistributionService::class);
        $mysqlService->expects(self::once())->method('createDatabase');
        $mysqlService->expects(self::once())->method('createUsers');

        $tripleStore = $this->tripleStoreService();
        $tripleStore->expects(self::never())->method('createDatabase');
        $tripleStore->expects(self::never())->method('createUsers');

        $handler = new CreateRDFDistributionCommandHandler(
            $em,
            $mysqlService,
            $tripleStore,
            $security,
            new EncryptionService(self::ENCRYPTION_KEY),
        );

        $distribution = $handler($command);

        self::assertSame('my-distribution', $distribution->getSlug());
        self::assertSame($license, $distribution->getLicense());

        $contents = $distribution->getContents();
        self::assertInstanceOf(RDFDistribution::class, $contents);
        // The contents default to the MySQL database type.
        self::assertTrue($contents->getDatabaseType()->isMysql());

        $databaseInformation = $distribution->getDatabaseInformation();
        self::assertNotNull($databaseInformation);
        // Usernames are derived from the same random byte sequence with the configured prefixes.
        self::assertStringStartsWith('fdp_dist_', $databaseInformation->getDatabase());
    }

    public function testStoresApiUserCredentialsWhenSupplied(): void
    {
        $dataset = $this->dataset();
        $command = $this->command(
            $dataset,
            'api-user@example.com',
            new SensitiveDataString('client-id'),
            new SensitiveDataString('client-secret'),
        );

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [MetadataModel::class, $this->repositoryReturning(self::METADATA_MODEL_ID, new MetadataModel('Metadata model', null))],
            [License::class, $this->repositoryReturning(self::LICENSE_ID, null)],
            [DataModel::class, $this->repositoryReturning(self::DATA_MODEL_ID, $this->dataModel())],
        ]);

        $persisted = [];
        $em->method('persist')->willReturnCallback(function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;

            if (! ($entity instanceof Distribution)) {
                return;
            }

            $this->ensureDistributionId($entity);
        });
        $em->expects(self::exactly(2))->method('flush');

        $mysqlService = $this->createMock(MysqlBasedDistributionService::class);

        $handler = new CreateRDFDistributionCommandHandler(
            $em,
            $mysqlService,
            $this->tripleStoreService(),
            $this->grantedSecurity($dataset),
            new EncryptionService(self::ENCRYPTION_KEY),
        );

        $distribution = $handler($command);

        $apiUser = $distribution->getApiUser();
        self::assertNotNull($apiUser);
        self::assertSame('api-user@example.com', $apiUser->getEmailAddress());
        // The handler persists the freshly created ApiUser.
        self::assertContains($apiUser, $persisted);
    }

    public function testThrowsWhenUserHasNoEditPermissionAndNeverPersists(): void
    {
        $dataset = $this->dataset();
        $command = $this->command($dataset);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('getRepository');
        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new User(null));
        $security->method('isGranted')->with(DatasetVoter::EDIT, $dataset)->willReturn(false);

        $handler = new CreateRDFDistributionCommandHandler(
            $em,
            $this->createMock(MysqlBasedDistributionService::class),
            $this->tripleStoreService(),
            $security,
            new EncryptionService(self::ENCRYPTION_KEY),
        );

        $this->expectException(NoAccessPermission::class);
        $handler($command);
    }

    public function testRollsBackDistributionWhenDatabaseProvisioningFails(): void
    {
        $dataset = $this->dataset();
        $command = $this->command($dataset);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [MetadataModel::class, $this->repositoryReturning(self::METADATA_MODEL_ID, new MetadataModel('Metadata model', null))],
            [License::class, $this->repositoryReturning(self::LICENSE_ID, null)],
            [DataModel::class, $this->repositoryReturning(self::DATA_MODEL_ID, $this->dataModel())],
        ]);
        $this->assignDistributionIdOnPersist($em);
        // The rollback removes both the distribution and the contents.
        $em->expects(self::exactly(2))->method('remove');

        $mysqlService = $this->createMock(MysqlBasedDistributionService::class);
        $mysqlService->method('createDatabase')->willThrowException(new RuntimeException('boom'));

        $handler = new CreateRDFDistributionCommandHandler(
            $em,
            $mysqlService,
            $this->tripleStoreService(),
            $this->grantedSecurity($dataset),
            new EncryptionService(self::ENCRYPTION_KEY),
        );

        $this->expectException(RuntimeException::class);
        $handler($command);
    }

    private function command(
        Dataset $dataset,
        ?string $apiUser = null,
        ?SensitiveDataString $clientId = null,
        ?SensitiveDataString $clientSecret = null,
    ): CreateRDFDistributionCommand {
        return new CreateRDFDistributionCommand(
            'my-distribution',
            self::METADATA_MODEL_ID,
            self::LICENSE_ID,
            $dataset,
            $apiUser,
            $clientId,
            $clientSecret,
            self::DATA_MODEL_ID,
            'data-model-version-id',
        );
    }

    private function dataset(): Dataset
    {
        $study = new CastorStudy('source-1', 'Study', 'study');
        $study->setServer(CastorServer::defaultServer('https://data.castoredc.com', 'data.castoredc.com', 'nl'));

        $dataset = new Dataset('my-dataset');
        $dataset->setStudy($study);

        return $dataset;
    }

    private function dataModel(): DataModel
    {
        $dataModel = new DataModel('Data model', null);
        $dataModel->addVersion(new DataModelVersion(new VersionNumber('1.0.0')));

        return $dataModel;
    }

    private function grantedSecurity(Dataset $dataset): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new User(null));
        $security->method('isGranted')->with(DatasetVoter::EDIT, $dataset)->willReturn(true);

        return $security;
    }

    private function repositoryReturning(string $id, ?object $result): EntityRepository
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($id)->willReturn($result);

        return $repository;
    }

    /**
     * The handler requires the deprecated Stardog service as a constructor collaborator; we only
     * mock it and assert the Stardog path is never taken (MySQL is the only supported path).
     *
     * @phpstan-ignore return.deprecatedClass
     */
    private function tripleStoreService(): MockObject&TripleStoreBasedDistributionService
    {
        /** @phpstan-ignore classConstant.deprecatedClass */
        return $this->createMock(TripleStoreBasedDistributionService::class);
    }

    /**
     * Mimics the real EntityManager assigning a generated UUID to the Distribution on persist/flush.
     * Without an id, DistributionDatabaseInformation's constructor (which reads getId()) would fail.
     */
    private function assignDistributionIdOnPersist(MockObject&EntityManagerInterface $em): void
    {
        $em->method('persist')->willReturnCallback(function (object $entity): void {
            if (! $entity instanceof Distribution) {
                return;
            }

            $this->ensureDistributionId($entity);
        });
    }

    private function ensureDistributionId(Distribution $distribution): void
    {
        $reflection = new ReflectionProperty(Distribution::class, 'id');

        if ($reflection->isInitialized($distribution)) {
            return;
        }

        $this->setEntityId($distribution, '11111111-1111-1111-1111-111111111111');
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\CreateCatalogMetadataCommand;
use App\CommandHandler\Metadata\CreateCatalogMetadataCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Catalog;
use App\Entity\Metadata\CatalogMetadata;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Service\VersionNumberHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateCatalogMetadataCommandHandlerTest extends TestCase
{
    public function testCreatesAndPersistsMetadata(): void
    {
        $catalog = $this->createMock(Catalog::class);
        $catalog->method('getLatestMetadataVersion')->willReturn(null);

        $version = new Version('1.0.0');
        $modelVersion = $this->createMock(MetadataModelVersion::class);
        $model = $this->createMock(MetadataModel::class);
        $modelVersion->method('getMetadataModel')->willReturn($model);

        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with(CatalogVoter::EDIT, $catalog)
            ->willReturn(true);

        $versionHelper = $this->createMock(VersionNumberHelper::class);
        $versionHelper->expects(self::once())
            ->method('getNewVersion')
            ->with(null, self::isInstanceOf(VersionType::class))
            ->willReturn($version);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(
            function (string $class) use ($model, $modelVersion): EntityRepository {
                $repository = $this->createMock(EntityRepository::class);
                $repository->method('find')->willReturn($class === MetadataModel::class ? $model : $modelVersion);

                return $repository;
            }
        );

        $catalog->expects(self::once())->method('addMetadata')->with(self::isInstanceOf(CatalogMetadata::class));

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $handler = new CreateCatalogMetadataCommandHandler($em, $security, $versionHelper);
        $handler(new CreateCatalogMetadataCommand($catalog, VersionType::major(), 'model-1', 'version-1'));

        self::assertContains($catalog, $persisted);
        self::assertCount(2, $persisted);
    }

    public function testThrowsWhenUserCannotEdit(): void
    {
        $catalog = $this->createMock(Catalog::class);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(CatalogVoter::EDIT, $catalog)->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = new CreateCatalogMetadataCommandHandler(
            $em,
            $security,
            $this->createMock(VersionNumberHelper::class),
        );

        $this->expectException(NoAccessPermission::class);

        $handler(new CreateCatalogMetadataCommand($catalog, VersionType::major(), 'model-1', 'version-1'));
    }

    public function testThrowsWhenModelNotFound(): void
    {
        $this->runNotFoundScenario(model: null, version: $this->createMock(MetadataModelVersion::class), matchesModel: false);
    }

    public function testThrowsWhenVersionNotFound(): void
    {
        $this->runNotFoundScenario(model: $this->createMock(MetadataModel::class), version: null, matchesModel: false);
    }

    public function testThrowsWhenVersionBelongsToDifferentModel(): void
    {
        $model = $this->createMock(MetadataModel::class);
        $version = $this->createMock(MetadataModelVersion::class);
        // Version's model differs from the looked-up model.
        $version->method('getMetadataModel')->willReturn($this->createMock(MetadataModel::class));

        $this->runNotFoundScenario(model: $model, version: $version, matchesModel: false);
    }

    private function runNotFoundScenario(?MetadataModel $model, ?MetadataModelVersion $version, bool $matchesModel): void
    {
        $catalog = $this->createMock(Catalog::class);
        $catalog->method('getLatestMetadataVersion')->willReturn(null);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(true);

        $versionHelper = $this->createMock(VersionNumberHelper::class);
        $versionHelper->method('getNewVersion')->willReturn(new Version('1.0.0'));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(
            function (string $class) use ($model, $version): EntityRepository {
                $repository = $this->createMock(EntityRepository::class);
                $repository->method('find')->willReturn($class === MetadataModel::class ? $model : $version);

                return $repository;
            }
        );
        $em->expects(self::never())->method('flush');

        $handler = new CreateCatalogMetadataCommandHandler($em, $security, $versionHelper);

        $this->expectException(NotFound::class);

        $handler(new CreateCatalogMetadataCommand($catalog, VersionType::major(), 'model-1', 'version-1'));
    }
}

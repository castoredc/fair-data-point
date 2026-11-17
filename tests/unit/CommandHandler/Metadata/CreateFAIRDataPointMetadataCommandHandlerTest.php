<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\CreateFAIRDataPointMetadataCommand;
use App\CommandHandler\Metadata\CreateFAIRDataPointMetadataCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Metadata\FAIRDataPointMetadata;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Service\VersionNumberHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateFAIRDataPointMetadataCommandHandlerTest extends TestCase
{
    public function testCreatesMetadataForFirstFairDataPoint(): void
    {
        $fdp = $this->createMock(FAIRDataPoint::class);
        $fdp->method('getLatestMetadataVersion')->willReturn(null);

        $model = $this->createMock(MetadataModel::class);
        $modelVersion = $this->createMock(MetadataModelVersion::class);
        $modelVersion->method('getMetadataModel')->willReturn($model);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $versionHelper = $this->createMock(VersionNumberHelper::class);
        $versionHelper->method('getNewVersion')->willReturn(new Version('1.0.0'));

        $fdpRepository = $this->createMock(EntityRepository::class);
        // findAll returns multiple; handler only uses the first.
        $fdpRepository->method('findAll')->willReturn([$fdp, $this->createMock(FAIRDataPoint::class)]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(
            function (string $class) use ($fdpRepository, $model, $modelVersion): EntityRepository {
                if ($class === FAIRDataPoint::class) {
                    return $fdpRepository;
                }

                $repository = $this->createMock(EntityRepository::class);
                $repository->method('find')->willReturn($class === MetadataModel::class ? $model : $modelVersion);

                return $repository;
            }
        );

        $fdp->expects(self::once())->method('addMetadata')->with(self::isInstanceOf(FAIRDataPointMetadata::class));
        $em->expects(self::once())->method('flush');

        $handler = new CreateFAIRDataPointMetadataCommandHandler($em, $security, $versionHelper);
        $handler(new CreateFAIRDataPointMetadataCommand(VersionType::major(), 'model-1', 'version-1'));
    }

    public function testThrowsWhenUserIsNotAdmin(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');
        $em->expects(self::never())->method('getRepository');

        $handler = new CreateFAIRDataPointMetadataCommandHandler(
            $em,
            $security,
            $this->createMock(VersionNumberHelper::class),
        );

        $this->expectException(NoAccessPermission::class);

        $handler(new CreateFAIRDataPointMetadataCommand(VersionType::major(), 'model-1', 'version-1'));
    }
}

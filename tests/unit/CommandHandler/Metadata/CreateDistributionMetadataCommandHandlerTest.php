<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\CreateDistributionMetadataCommand;
use App\CommandHandler\Metadata\CreateDistributionMetadataCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Distribution;
use App\Entity\Metadata\DistributionMetadata;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DistributionVoter;
use App\Service\VersionNumberHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateDistributionMetadataCommandHandlerTest extends TestCase
{
    public function testCreatesAndPersistsMetadata(): void
    {
        $distribution = $this->createMock(Distribution::class);
        $distribution->method('getLatestMetadataVersion')->willReturn(null);

        $model = $this->createMock(MetadataModel::class);
        $modelVersion = $this->createMock(MetadataModelVersion::class);
        $modelVersion->method('getMetadataModel')->willReturn($model);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(DistributionVoter::EDIT, $distribution)->willReturn(true);

        $versionHelper = $this->createMock(VersionNumberHelper::class);
        $versionHelper->method('getNewVersion')->willReturn(new Version('1.0.0'));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(
            function (string $class) use ($model, $modelVersion): EntityRepository {
                $repository = $this->createMock(EntityRepository::class);
                $repository->method('find')->willReturn($class === MetadataModel::class ? $model : $modelVersion);

                return $repository;
            }
        );

        $distribution->expects(self::once())
            ->method('addMetadata')
            ->with(self::isInstanceOf(DistributionMetadata::class));
        $em->expects(self::once())->method('flush');

        $handler = new CreateDistributionMetadataCommandHandler($em, $security, $versionHelper);
        $handler(new CreateDistributionMetadataCommand($distribution, VersionType::patch(), 'model-1', 'version-1'));
    }

    public function testThrowsWhenUserCannotEdit(): void
    {
        $distribution = $this->createMock(Distribution::class);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(DistributionVoter::EDIT, $distribution)->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = new CreateDistributionMetadataCommandHandler(
            $em,
            $security,
            $this->createMock(VersionNumberHelper::class),
        );

        $this->expectException(NoAccessPermission::class);

        $handler(new CreateDistributionMetadataCommand($distribution, VersionType::patch(), 'model-1', 'version-1'));
    }
}

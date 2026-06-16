<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\CreateStudyMetadataCommand;
use App\CommandHandler\Metadata\CreateStudyMetadataCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\VersionType;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\StudyVoter;
use App\Service\VersionNumberHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateStudyMetadataCommandHandlerTest extends TestCase
{
    public function testCreatesAndPersistsMetadata(): void
    {
        $study = $this->createMock(Study::class);
        $study->method('getLatestMetadataVersion')->willReturn(null);

        $model = $this->createMock(MetadataModel::class);
        $modelVersion = $this->createMock(MetadataModelVersion::class);
        $modelVersion->method('getMetadataModel')->willReturn($model);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(StudyVoter::EDIT, $study)->willReturn(true);

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

        $study->expects(self::once())->method('addMetadata')->with(self::isInstanceOf(StudyMetadata::class));
        $em->expects(self::once())->method('flush');

        $handler = new CreateStudyMetadataCommandHandler($em, $security, $versionHelper);
        $handler(new CreateStudyMetadataCommand($study, VersionType::major(), 'model-1', 'version-1'));
    }

    public function testThrowsWhenUserCannotEdit(): void
    {
        $study = $this->createMock(Study::class);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(StudyVoter::EDIT, $study)->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = new CreateStudyMetadataCommandHandler(
            $em,
            $security,
            $this->createMock(VersionNumberHelper::class),
        );

        $this->expectException(NoAccessPermission::class);

        $handler(new CreateStudyMetadataCommand($study, VersionType::major(), 'model-1', 'version-1'));
    }
}

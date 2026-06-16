<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\ImportDataModelVersionCommand;
use App\CommandHandler\DataSpecification\DataModel\ImportDataModelVersionCommandHandler;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Version;
use App\Exception\DataSpecification\DataModel\InvalidDataModelVersion;
use App\Exception\NoAccessPermission;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Factory\DataSpecification\DataModel\DataModelModuleFactory;
use App\Factory\DataSpecification\DataModel\NamespacePrefixFactory;
use App\Factory\DataSpecification\DataModel\NodeFactory;
use App\Factory\DataSpecification\DataModel\PredicateFactory;
use App\Factory\DataSpecification\DataModel\TripleFactory;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class ImportDataModelVersionCommandHandlerTest extends TestCase
{
    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }

        $this->tempFiles = [];
    }

    public function testThrowsWhenNotGranted(): void
    {
        $model = new DataModel('Model', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $model)
            ->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler(new ImportDataModelVersionCommand($model, $this->validFile('{}'), new Version('2.0.0')));
    }

    public function testThrowsWhenFileInvalid(): void
    {
        $model = new DataModel('Model', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $file = $this->createMock(UploadedFile::class);
        $file->method('isValid')->willReturn(false);
        $file->method('getErrorMessage')->willReturn('boom');

        $handler = $this->buildHandler($em, $this->grantedSecurity($model));

        $this->expectException(InvalidFile::class);
        $handler(new ImportDataModelVersionCommand($model, $file, new Version('2.0.0')));
    }

    public function testThrowsWhenJsonInvalid(): void
    {
        $model = new DataModel('Model', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em, $this->grantedSecurity($model));

        $this->expectException(InvalidJSON::class);
        $handler(new ImportDataModelVersionCommand($model, $this->validFile('not json'), new Version('2.0.0')));
    }

    public function testThrowsWhenVersionAlreadyExists(): void
    {
        $model = new DataModel('Model', null);
        $version = new Version('2.0.0');
        $existing = new DataModelVersion($version);
        $model->addVersion($existing);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em, $this->grantedSecurity($model));

        $this->expectException(InvalidDataModelVersion::class);
        $handler(new ImportDataModelVersionCommand($model, $this->validFile($this->emptyPayload()), $version));
    }

    public function testImportsEmptyPayload(): void
    {
        $model = new DataModel('Model', null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = $this->buildHandler($em, $this->grantedSecurity($model));
        $newVersion = $handler(new ImportDataModelVersionCommand($model, $this->validFile($this->emptyPayload()), new Version('2.0.0')));

        self::assertSame('2.0.0', $newVersion->getVersion()->getValue());
        self::assertCount(1, $model->getVersions());
        self::assertSame($newVersion, $model->getLatestVersion());
        self::assertCount(0, $newVersion->getElements());
        self::assertCount(0, $newVersion->getPredicates());
        self::assertCount(0, $newVersion->getGroups());
    }

    private function emptyPayload(): string
    {
        return '{"nodes":[],"modules":[],"prefixes":[],"predicates":[]}';
    }

    private function grantedSecurity(DataModel $model): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $model)
            ->willReturn(true);

        return $security;
    }

    private function buildHandler(EntityManagerInterface $em, Security $security): ImportDataModelVersionCommandHandler
    {
        return new ImportDataModelVersionCommandHandler(
            $em,
            $security,
            $this->createMock(NamespacePrefixFactory::class),
            $this->createMock(NodeFactory::class),
            $this->createMock(PredicateFactory::class),
            $this->createMock(DataModelModuleFactory::class),
            $this->createMock(TripleFactory::class),
        );
    }

    private function validFile(string $contents): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'import');
        self::assertIsString($path);
        file_put_contents($path, $contents);
        $this->tempFiles[] = $path;

        $file = $this->createMock(UploadedFile::class);
        $file->method('isValid')->willReturn(true);
        $file->method('getPathname')->willReturn($path);

        return $file;
    }
}

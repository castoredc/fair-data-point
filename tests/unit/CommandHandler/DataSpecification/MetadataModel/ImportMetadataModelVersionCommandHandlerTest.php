<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\ImportMetadataModelVersionCommand;
use App\CommandHandler\DataSpecification\MetadataModel\ImportMetadataModelVersionCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Version;
use App\Exception\DataSpecification\MetadataModel\InvalidMetadataModelVersion;
use App\Exception\Upload\InvalidJSON;
use App\Factory\DataSpecification\MetadataModel\DisplaySettingFactory;
use App\Factory\DataSpecification\MetadataModel\FormFactory;
use App\Factory\DataSpecification\MetadataModel\MetadataModelModuleFactory;
use App\Factory\DataSpecification\MetadataModel\NamespacePrefixFactory;
use App\Factory\DataSpecification\MetadataModel\NodeFactory;
use App\Factory\DataSpecification\MetadataModel\OptionGroupFactory;
use App\Factory\DataSpecification\MetadataModel\PredicateFactory;
use App\Factory\DataSpecification\MetadataModel\TripleFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * Note: this handler bypasses the permission and file-validity guards when
 * PHP_SAPI === 'cli' (which is the case under PHPUnit). Those branches are
 * therefore exercised in integration tests, not here. This unit test covers
 * the JSON parsing, version-collision and happy-path branches.
 */
final class ImportMetadataModelVersionCommandHandlerTest extends TestCase
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

    public function testThrowsWhenJsonInvalid(): void
    {
        $model = new MetadataModel('Model', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em);

        $this->expectException(InvalidJSON::class);
        $handler(new ImportMetadataModelVersionCommand($model, $this->validFile('garbage'), new Version('2.0.0')));
    }

    public function testThrowsWhenVersionAlreadyExists(): void
    {
        $model = new MetadataModel('Model', null);
        $version = new Version('2.0.0');
        $model->addVersion(new MetadataModelVersion($version));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em);

        $this->expectException(InvalidMetadataModelVersion::class);
        $handler(new ImportMetadataModelVersionCommand($model, $this->validFile($this->emptyPayload()), $version));
    }

    public function testImportsEmptyPayload(): void
    {
        $model = new MetadataModel('Model', null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = $this->buildHandler($em);
        $newVersion = $handler(new ImportMetadataModelVersionCommand($model, $this->validFile($this->emptyPayload()), new Version('2.0.0')));

        self::assertSame('2.0.0', $newVersion->getVersion()->getValue());
        self::assertCount(1, $model->getVersions());
        self::assertSame($newVersion, $model->getLatestVersion());
        self::assertCount(0, $newVersion->getElements());
        self::assertCount(0, $newVersion->getForms());
    }

    private function emptyPayload(): string
    {
        return '{"nodes":[],"modules":[],"prefixes":[],"predicates":[],"optionGroups":[],"forms":[],"displaySettings":[]}';
    }

    private function buildHandler(EntityManagerInterface $em): ImportMetadataModelVersionCommandHandler
    {
        return new ImportMetadataModelVersionCommandHandler(
            $em,
            $this->createMock(Security::class),
            $this->createMock(NamespacePrefixFactory::class),
            $this->createMock(NodeFactory::class),
            $this->createMock(PredicateFactory::class),
            $this->createMock(OptionGroupFactory::class),
            $this->createMock(FormFactory::class),
            $this->createMock(MetadataModelModuleFactory::class),
            $this->createMock(TripleFactory::class),
            $this->createMock(DisplaySettingFactory::class),
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

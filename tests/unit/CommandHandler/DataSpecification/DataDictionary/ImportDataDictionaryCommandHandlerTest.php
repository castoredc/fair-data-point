<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\DataDictionary;

use App\Command\DataSpecification\DataDictionary\ImportDataDictionaryCommand;
use App\CommandHandler\DataSpecification\DataDictionary\ImportDataDictionaryCommandHandler;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Entity\Version;
use App\Exception\DataSpecification\DataDictionary\InvalidDataDictionaryVersion;
use App\Exception\NoAccessPermission;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Factory\DataSpecification\DataDictionary\DataDictionaryGroupFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class ImportDataDictionaryCommandHandlerTest extends TestCase
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

    public function testThrowsWhenNotAdmin(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler(new ImportDataDictionaryCommand($dictionary, $this->validFile('{}'), new Version('2.0.0')));
    }

    public function testThrowsWhenFileInvalid(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $file = $this->createMock(UploadedFile::class);
        $file->method('isValid')->willReturn(false);
        $file->method('getErrorMessage')->willReturn('boom');

        $handler = $this->buildHandler($em, $this->adminSecurity());

        $this->expectException(InvalidFile::class);
        $handler(new ImportDataDictionaryCommand($dictionary, $file, new Version('2.0.0')));
    }

    public function testThrowsWhenJsonInvalid(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em, $this->adminSecurity());

        $this->expectException(InvalidJSON::class);
        $handler(new ImportDataDictionaryCommand($dictionary, $this->validFile('nope'), new Version('2.0.0')));
    }

    public function testThrowsWhenVersionAlreadyExists(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);
        $version = new Version('2.0.0');
        $dictionary->addVersion(new DataDictionaryVersion($version));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = $this->buildHandler($em, $this->adminSecurity());

        $this->expectException(InvalidDataDictionaryVersion::class);
        $handler(new ImportDataDictionaryCommand($dictionary, $this->validFile('{"groups":[]}'), $version));
    }

    public function testImportsEmptyPayload(): void
    {
        $dictionary = new DataDictionary('Dictionary', null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = $this->buildHandler($em, $this->adminSecurity());
        $newVersion = $handler(new ImportDataDictionaryCommand($dictionary, $this->validFile('{"groups":[]}'), new Version('2.0.0')));

        self::assertSame('2.0.0', $newVersion->getVersion()->getValue());
        self::assertCount(1, $dictionary->getVersions());
        self::assertSame($newVersion, $dictionary->getLatestVersion());
        self::assertCount(0, $newVersion->getGroups());
    }

    private function adminSecurity(): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        return $security;
    }

    private function buildHandler(EntityManagerInterface $em, Security $security): ImportDataDictionaryCommandHandler
    {
        return new ImportDataDictionaryCommandHandler(
            $em,
            $security,
            $this->createMock(DataDictionaryGroupFactory::class),
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

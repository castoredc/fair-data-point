<?php
declare(strict_types=1);

namespace App\Tests\unit\Entity;

use App\Entity\Version;
use App\Exception\InvalidVersion;
use Error;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VersionTest extends TestCase
{
    #[DataProvider('provideValidVersions')]
    public function testParsesValidVersionString(string $version, int $major, int $minor, int $patch): void
    {
        $parsed = new Version($version);

        self::assertSame($major, $parsed->getMajor());
        self::assertSame($minor, $parsed->getMinor());
        self::assertSame($patch, $parsed->getPatch());
        self::assertSame($version, $parsed->getValue());
        self::assertSame($version, (string) $parsed);
    }

    /** @return array<string, array{string, int, int, int}> */
    public static function provideValidVersions(): array
    {
        return [
            'simple version' => ['1.2.3', 1, 2, 3],
            'multi-digit version' => ['10.20.30', 10, 20, 30],
            'zero version' => ['0.0.0', 0, 0, 0],
        ];
    }

    #[DataProvider('provideInvalidVersions')]
    public function testRejectsInvalidVersionString(string $version): void
    {
        $this->expectException(InvalidVersion::class);

        new Version($version);
    }

    /** @return array<string, array<string>> */
    public static function provideInvalidVersions(): array
    {
        return [
            'empty string' => [''],
            'major only' => ['1'],
            'major and minor only' => ['1.2'],
            'non-numeric part' => ['1.2.x'],
            'leading v' => ['v1.2.3'],
            'four parts' => ['1.2.3.4'],
            'pre-release suffix' => ['1.2.3-beta'],
        ];
    }

    public function testVersionWithoutInitialValueCanBePopulatedWithSetters(): void
    {
        $version = new Version(null);

        $version->setMajor(2);
        $version->setMinor(1);
        $version->setPatch(0);

        self::assertSame('2.1.0', $version->getValue());
    }

    public function testAccessingValueOfVersionWithoutInitialValueThrows(): void
    {
        $version = new Version(null);

        $this->expectException(Error::class);

        $version->getValue();
    }

    /**
     * The version regex uses the multiline flag, so any string that contains a valid
     * version on its own line is accepted and parsed from that line.
     */
    public function testAcceptsMultilineStringContainingAVersionLine(): void
    {
        $version = new Version("not a version\n1.2.3");

        self::assertSame('1.2.3', $version->getValue());
    }
}

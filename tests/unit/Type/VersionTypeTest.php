<?php
declare(strict_types=1);

namespace App\Tests\unit\Type;

use App\Entity\Version;
use App\Exception\InvalidVersion;
use App\Type\VersionType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VersionTypeTest extends TestCase
{
    private VersionType $type;
    private MySQLPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new VersionType();
        $this->platform = new MySQLPlatform();
    }

    #[DataProvider('provideVersions')]
    public function testDatabaseValueRoundTripsThroughPhpValue(string $version): void
    {
        $phpValue = $this->type->convertToPHPValue($version, $this->platform);

        self::assertInstanceOf(Version::class, $phpValue);
        self::assertSame($version, $phpValue->getValue());
        self::assertSame($version, $this->type->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideVersions')]
    public function testPhpValueRoundTripsThroughDatabaseValue(string $version): void
    {
        $databaseValue = $this->type->convertToDatabaseValue(new Version($version), $this->platform);

        self::assertSame($version, $databaseValue);

        $rehydrated = $this->type->convertToPHPValue($databaseValue, $this->platform);

        self::assertInstanceOf(Version::class, $rehydrated);
        self::assertSame($version, $rehydrated->getValue());
    }

    public function testHydratedVersionExposesItsParts(): void
    {
        $phpValue = $this->type->convertToPHPValue('10.2.33', $this->platform);

        self::assertInstanceOf(Version::class, $phpValue);
        self::assertSame(10, $phpValue->getMajor());
        self::assertSame(2, $phpValue->getMinor());
        self::assertSame(33, $phpValue->getPatch());
    }

    public function testConvertToPHPValueReturnsNullForNullAndEmptyString(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
        self::assertNull($this->type->convertToPHPValue('', $this->platform));
    }

    public function testConvertToDatabaseValueMapsNullAndEmptyStringToEmptyString(): void
    {
        // Note: null is stored as an empty string, not as SQL NULL.
        self::assertSame('', $this->type->convertToDatabaseValue(null, $this->platform));
        self::assertSame('', $this->type->convertToDatabaseValue('', $this->platform));
    }

    #[DataProvider('provideInvalidVersions')]
    public function testConvertToPHPValueRejectsInvalidVersionStrings(string $invalidVersion): void
    {
        $this->expectException(InvalidVersion::class);

        $this->type->convertToPHPValue($invalidVersion, $this->platform);
    }

    public function testLeadingZerosAreNormalizedAndDoNotRoundTripVerbatim(): void
    {
        // Current behaviour: Version casts each part to int, so '01.02.03' becomes '1.2.3'.
        $phpValue = $this->type->convertToPHPValue('01.02.03', $this->platform);

        self::assertInstanceOf(Version::class, $phpValue);
        self::assertSame('1.2.3', $this->type->convertToDatabaseValue($phpValue, $this->platform));
    }

    /** @return array<string, array{string}> */
    public static function provideVersions(): array
    {
        return [
            'initial release' => ['1.0.0'],
            'multi-digit parts' => ['10.2.33'],
            'zero version' => ['0.0.0'],
            'large parts' => ['123.456.789'],
        ];
    }

    /** @return array<string, array{string}> */
    public static function provideInvalidVersions(): array
    {
        return [
            'major and minor only' => ['1.0'],
            'four parts' => ['1.0.0.0'],
            'leading v prefix' => ['v1.0.0'],
            'plain text' => ['not-a-version'],
            'non-numeric patch' => ['1.0.x'],
            'negative part' => ['1.-2.3'],
        ];
    }
}

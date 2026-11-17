<?php
declare(strict_types=1);

namespace App\Tests\unit\Type;

use App\Entity\Iri;
use App\Type\IriType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IriTypeTest extends TestCase
{
    private IriType $type;
    private MySQLPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new IriType();
        $this->platform = new MySQLPlatform();
    }

    #[DataProvider('provideIris')]
    public function testDatabaseValueRoundTripsThroughPhpValue(string $iri): void
    {
        $phpValue = $this->type->convertToPHPValue($iri, $this->platform);

        self::assertInstanceOf(Iri::class, $phpValue);
        self::assertSame($iri, $phpValue->getValue());
        self::assertSame($iri, $this->type->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideIris')]
    public function testPhpValueRoundTripsThroughDatabaseValue(string $iri): void
    {
        $databaseValue = $this->type->convertToDatabaseValue(new Iri($iri), $this->platform);

        self::assertSame($iri, $databaseValue);

        $rehydrated = $this->type->convertToPHPValue($databaseValue, $this->platform);

        self::assertInstanceOf(Iri::class, $rehydrated);
        self::assertSame($iri, $rehydrated->getValue());
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

    public function testConvertToDatabaseValueAcceptsAPlainString(): void
    {
        self::assertSame(
            'https://example.com/dataset/1',
            $this->type->convertToDatabaseValue('https://example.com/dataset/1', $this->platform),
        );
    }

    public function testDoesNotValidateIriSyntax(): void
    {
        // Current behaviour: any non-empty string is accepted as an IRI.
        $phpValue = $this->type->convertToPHPValue('not a valid iri at all', $this->platform);

        self::assertInstanceOf(Iri::class, $phpValue);
        self::assertSame('not a valid iri at all', $phpValue->getValue());
    }

    /** @return array<string, array{string}> */
    public static function provideIris(): array
    {
        return [
            'simple https IRI' => ['https://example.com/resource/1'],
            'IRI with query string and fragment' => ['https://example.com/path?query=value&other=1#section-2'],
            'IRI with unicode characters' => ['https://例え.jp/データ/概要?q=研究'],
            'IRI with encoded characters' => ['https://example.com/a%20b/c?d=e%26f'],
            'urn IRI' => ['urn:uuid:6f8de8f6-9cf6-43c8-a4e9-8a0182371b04'],
            'purl IRI' => ['http://purl.org/dc/terms/title'],
        ];
    }
}

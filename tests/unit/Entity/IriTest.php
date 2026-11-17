<?php
declare(strict_types=1);

namespace App\Tests\unit\Entity;

use App\Entity\Iri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IriTest extends TestCase
{
    public function testExposesValueThroughGetValueAndToString(): void
    {
        $iri = new Iri('http://purl.org/dc/terms/title');

        self::assertSame('http://purl.org/dc/terms/title', $iri->getValue());
        self::assertSame('http://purl.org/dc/terms/title', (string) $iri);
    }

    #[DataProvider('provideBasesAndPrefixes')]
    public function testSplitsIriIntoBaseAndPrefix(string $iri, string $expectedBase, string $expectedPrefix): void
    {
        self::assertSame($expectedBase, (new Iri($iri))->getBase());
        self::assertSame($expectedPrefix, (new Iri($iri))->getPrefix());
    }

    /** @return array<string, array{string, string, string}> */
    public static function provideBasesAndPrefixes(): array
    {
        return [
            'slash-based iri' => ['http://purl.org/dc/terms/title', 'title', 'http://purl.org/dc/terms/'],
            'hash-based iri uses the fragment' => [
                'http://www.w3.org/2001/XMLSchema#string',
                'string',
                'http://www.w3.org/2001/XMLSchema#',
            ],
            'trailing slash is ignored for the base' => ['https://example.com/path/', 'path', 'https://example.com/'],
            'plain string without separators' => ['foo', 'foo', ''],
            'empty iri' => ['', '', ''],
        ];
    }
}

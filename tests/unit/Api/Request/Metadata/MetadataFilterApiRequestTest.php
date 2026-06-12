<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Metadata;

use App\Api\Request\Metadata\MetadataFilterApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MetadataFilterApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessQueryParameters(): void
    {
        $apiRequest = new MetadataFilterApiRequest(
            self::createQueryRequest(['search' => 'covid', 'perPage' => '50', 'page' => '2'])
        );

        self::assertSame('covid', $apiRequest->getSearch());
        self::assertSame(50, $apiRequest->getPerPage());
        self::assertSame(2, $apiRequest->getPage());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testFallsBackToDefaultsWhenParametersAreMissing(): void
    {
        $apiRequest = new MetadataFilterApiRequest(self::createQueryRequest([]));

        self::assertNull($apiRequest->getSearch());
        self::assertSame(MetadataFilterApiRequest::DEFAULT_PER_PAGE, $apiRequest->getPerPage());
        self::assertSame(1, $apiRequest->getPage());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testEmptySearchBecomesNull(): void
    {
        $apiRequest = new MetadataFilterApiRequest(self::createQueryRequest(['search' => '']));

        self::assertNull($apiRequest->getSearch());
    }

    #[DataProvider('provideNonPositiveIntegers')]
    public function testZeroOrNonNumericPaginationFallsBackToDefaults(string $value): void
    {
        $apiRequest = new MetadataFilterApiRequest(
            self::createQueryRequest(['perPage' => $value, 'page' => $value])
        );

        self::assertSame(MetadataFilterApiRequest::DEFAULT_PER_PAGE, $apiRequest->getPerPage());
        self::assertSame(1, $apiRequest->getPage());
    }

    /** @return array<string, array{string}> */
    public static function provideNonPositiveIntegers(): array
    {
        return [
            'zero' => ['0'],
            'non-numeric' => ['abc'],
            'empty string' => [''],
        ];
    }

    /**
     * Suspected dead code: parse() never reads hideParents, so the filter is
     * always null no matter what the client sends.
     */
    public function testHideParentsIsNeverParsed(): void
    {
        $apiRequest = new MetadataFilterApiRequest(
            self::createQueryRequest(['hideParents' => ['parent-1', 'parent-2']])
        );

        self::assertNull($apiRequest->getHideParents());
    }
}

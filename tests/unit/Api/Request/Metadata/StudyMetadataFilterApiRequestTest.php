<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Metadata;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\TestCase;

final class StudyMetadataFilterApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessQueryParameters(): void
    {
        $apiRequest = new StudyMetadataFilterApiRequest(
            self::createQueryRequest(
                [
                    'perPage' => '10',
                    'page' => '3',
                    'hideCatalogs' => ['catalog-1', 'catalog-2'],
                ]
            )
        );

        self::assertSame(10, $apiRequest->getPerPage());
        self::assertSame(3, $apiRequest->getPage());
        self::assertSame(['catalog-1', 'catalog-2'], $apiRequest->getHideCatalogs());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testFallsBackToDefaultsWhenParametersAreMissing(): void
    {
        $apiRequest = new StudyMetadataFilterApiRequest(self::createQueryRequest([]));

        self::assertSame(StudyMetadataFilterApiRequest::DEFAULT_PER_PAGE, $apiRequest->getPerPage());
        self::assertSame(1, $apiRequest->getPage());
        self::assertNull($apiRequest->getHideCatalogs());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }
}

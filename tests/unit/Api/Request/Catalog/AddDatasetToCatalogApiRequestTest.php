<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Catalog;

use App\Api\Request\Catalog\AddDatasetToCatalogApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AddDatasetToCatalogApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidRequest(): void
    {
        $apiRequest = new AddDatasetToCatalogApiRequest(
            self::createJsonRequest(['datasetId' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479'])
        );

        self::assertSame('f47ac10b-58cc-4372-a567-0e02b2c3d479', $apiRequest->getDatasetId());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /** @param array<mixed> $data */
    #[DataProvider('provideBlankPayloads')]
    public function testBlankDatasetIdFailsValidation(array $data): void
    {
        $apiRequest = new AddDatasetToCatalogApiRequest(self::createJsonRequest($data));

        $violations = self::createValidator()->validate($apiRequest);

        self::assertSame(['datasetId'], self::getViolatedPropertyPaths($violations));
    }

    /** @return array<string, array{array<mixed>}> */
    public static function provideBlankPayloads(): array
    {
        return [
            'missing datasetId' => [[]],
            'null datasetId' => [['datasetId' => null]],
            'empty datasetId' => [['datasetId' => '']],
            'whitespace datasetId' => [['datasetId' => '   ']],
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Catalog;

use App\Api\Request\Catalog\AddStudyToCatalogApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AddStudyToCatalogApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidRequest(): void
    {
        $apiRequest = new AddStudyToCatalogApiRequest(
            self::createJsonRequest(['studyId' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479'])
        );

        self::assertSame('f47ac10b-58cc-4372-a567-0e02b2c3d479', $apiRequest->getStudyId());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /** @param array<mixed> $data */
    #[DataProvider('provideBlankPayloads')]
    public function testBlankStudyIdFailsValidation(array $data): void
    {
        $apiRequest = new AddStudyToCatalogApiRequest(self::createJsonRequest($data));

        $violations = self::createValidator()->validate($apiRequest);

        self::assertSame(['studyId'], self::getViolatedPropertyPaths($violations));
    }

    /** @return array<string, array{array<mixed>}> */
    public static function provideBlankPayloads(): array
    {
        return [
            'missing studyId' => [[]],
            'null studyId' => [['studyId' => null]],
            'empty studyId' => [['studyId' => '']],
            'whitespace studyId' => [['studyId' => '   ']],
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Catalog;

use App\Api\Request\Catalog\CatalogApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;

final class CatalogApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidRequest(): void
    {
        $apiRequest = new CatalogApiRequest(
            self::createJsonRequest(
                [
                    'slug' => 'my-catalog',
                    'defaultMetadataModel' => 'b1f9a7f0-0000-0000-0000-000000000000',
                    'acceptSubmissions' => true,
                    'submissionAccessesData' => false,
                ]
            )
        );

        self::assertSame('my-catalog', $apiRequest->getSlug());
        self::assertSame('b1f9a7f0-0000-0000-0000-000000000000', $apiRequest->getDefaultMetadataModel());
        self::assertTrue($apiRequest->isAcceptSubmissions());
        self::assertFalse($apiRequest->isSubmissionAccessesData());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testSubmissionAccessesDataIsNullWhenMissing(): void
    {
        $apiRequest = new CatalogApiRequest(
            self::createJsonRequest(
                [
                    'slug' => 'my-catalog',
                    'defaultMetadataModel' => 'model',
                    'acceptSubmissions' => false,
                ]
            )
        );

        self::assertNull($apiRequest->isSubmissionAccessesData());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testSubmissionAccessesDataMayBeExplicitlyNull(): void
    {
        $apiRequest = new CatalogApiRequest(
            self::createJsonRequest(
                [
                    'slug' => 'my-catalog',
                    'defaultMetadataModel' => 'model',
                    'acceptSubmissions' => false,
                    'submissionAccessesData' => null,
                ]
            )
        );

        self::assertNull($apiRequest->isSubmissionAccessesData());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /**
     * Missing or wrongly typed required fields never reach validation:
     * parse() assigns an incompatible value to a typed property, causing a TypeError.
     *
     * @param array<mixed> $data
     */
    #[DataProvider('provideInvalidPayloads')]
    public function testInvalidRequiredFieldsThrowTypeErrorDuringParsing(array $data): void
    {
        $this->expectException(TypeError::class);

        new CatalogApiRequest(self::createJsonRequest($data));
    }

    /** @return array<string, array{array<mixed>}> */
    public static function provideInvalidPayloads(): array
    {
        $valid = [
            'slug' => 'my-catalog',
            'defaultMetadataModel' => 'model',
            'acceptSubmissions' => true,
        ];

        return [
            'missing slug' => [['defaultMetadataModel' => 'model', 'acceptSubmissions' => true]],
            'empty slug' => [['slug' => ''] + $valid],
            'non-string slug' => [['slug' => 5] + $valid],
            'missing defaultMetadataModel' => [['slug' => 'my-catalog', 'acceptSubmissions' => true]],
            'missing acceptSubmissions' => [['slug' => 'my-catalog', 'defaultMetadataModel' => 'model']],
            'string acceptSubmissions' => [['acceptSubmissions' => 'yes'] + $valid],
            'string submissionAccessesData' => [$valid + ['submissionAccessesData' => 'yes']],
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Dataset;

use App\Api\Request\Dataset\DatasetApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;

final class DatasetApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidRequest(): void
    {
        $apiRequest = new DatasetApiRequest(
            self::createJsonRequest(
                [
                    'slug' => 'my-dataset',
                    'defaultMetadataModel' => 'b1f9a7f0-0000-0000-0000-000000000000',
                    'published' => true,
                ]
            )
        );

        self::assertSame('my-dataset', $apiRequest->getSlug());
        self::assertSame('b1f9a7f0-0000-0000-0000-000000000000', $apiRequest->getDefaultMetadataModel());
        self::assertTrue($apiRequest->getPublished());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testTrimsStringValues(): void
    {
        $apiRequest = new DatasetApiRequest(
            self::createJsonRequest(
                [
                    'slug' => '  my-dataset  ',
                    'defaultMetadataModel' => " model\n",
                    'published' => false,
                ]
            )
        );

        self::assertSame('my-dataset', $apiRequest->getSlug());
        self::assertSame('model', $apiRequest->getDefaultMetadataModel());
    }

    public function testIgnoresUnknownExtraKeys(): void
    {
        $apiRequest = new DatasetApiRequest(
            self::createJsonRequest(
                [
                    'slug' => 'my-dataset',
                    'defaultMetadataModel' => 'model',
                    'published' => true,
                    'unknownKey' => 'is ignored',
                ]
            )
        );

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /**
     * The published flag is run through boolval(), so a missing key silently
     * becomes false and the NotNull constraint can never be violated.
     */
    #[DataProvider('providePublishedValues')]
    public function testPublishedIsCastWithBoolval(mixed $published, bool $expected): void
    {
        $data = [
            'slug' => 'my-dataset',
            'defaultMetadataModel' => 'model',
        ];

        if ($published !== null) {
            $data['published'] = $published;
        }

        $apiRequest = new DatasetApiRequest(self::createJsonRequest($data));

        self::assertSame($expected, $apiRequest->getPublished());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /** @return array<string, array{mixed, bool}> */
    public static function providePublishedValues(): array
    {
        return [
            'missing key becomes false' => [null, false],
            'true stays true' => [true, true],
            'false stays false' => [false, false],
            'integer 1 becomes true' => [1, true],
            'integer 0 becomes false' => [0, false],
            'string "false" becomes true' => ['false', true],
        ];
    }

    /**
     * Missing, empty or non-string required fields never reach validation:
     * parse() assigns null to a non-nullable typed property, causing a TypeError.
     *
     * @param array<mixed> $data
     */
    #[DataProvider('provideInvalidPayloads')]
    public function testInvalidRequiredFieldsThrowTypeErrorDuringParsing(array $data): void
    {
        $this->expectException(TypeError::class);

        new DatasetApiRequest(self::createJsonRequest($data));
    }

    /** @return array<string, array{array<mixed>}> */
    public static function provideInvalidPayloads(): array
    {
        return [
            'missing slug' => [['defaultMetadataModel' => 'model', 'published' => true]],
            'null slug' => [['slug' => null, 'defaultMetadataModel' => 'model', 'published' => true]],
            'empty slug' => [['slug' => '', 'defaultMetadataModel' => 'model', 'published' => true]],
            'whitespace slug' => [['slug' => '   ', 'defaultMetadataModel' => 'model', 'published' => true]],
            'non-string slug' => [['slug' => 123, 'defaultMetadataModel' => 'model', 'published' => true]],
            'missing defaultMetadataModel' => [['slug' => 'my-dataset', 'published' => true]],
            'empty defaultMetadataModel' => [['slug' => 'my-dataset', 'defaultMetadataModel' => '', 'published' => true]],
        ];
    }
}

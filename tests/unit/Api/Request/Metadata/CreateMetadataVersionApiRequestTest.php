<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Metadata;

use App\Api\Request\Metadata\CreateMetadataVersionApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;

final class CreateMetadataVersionApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    #[DataProvider('provideVersionTypes')]
    public function testCanProcessValidRequest(string $versionType): void
    {
        $apiRequest = new CreateMetadataVersionApiRequest(
            self::createJsonRequest(
                [
                    'versionType' => $versionType,
                    'model' => 'model-id',
                    'modelVersion' => 'model-version-id',
                ]
            )
        );

        self::assertSame($versionType, $apiRequest->getVersionType()->toString());
        self::assertSame('model-id', $apiRequest->getModel());
        self::assertSame('model-version-id', $apiRequest->getModelVersion());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /** @return array<string, array{string}> */
    public static function provideVersionTypes(): array
    {
        return [
            'major' => ['major'],
            'minor' => ['minor'],
            'patch' => ['patch'],
        ];
    }

    /**
     * An unknown versionType passes validation (only NotBlank and Type are checked);
     * the failure surfaces later as an exception when getVersionType() builds the enum.
     */
    public function testUnknownVersionTypePassesValidationButGetVersionTypeThrows(): void
    {
        $apiRequest = new CreateMetadataVersionApiRequest(
            self::createJsonRequest(
                [
                    'versionType' => 'huge',
                    'model' => 'model-id',
                    'modelVersion' => 'model-version-id',
                ]
            )
        );

        self::assertCount(0, self::createValidator()->validate($apiRequest));

        $this->expectException(InvalidArgumentException::class);

        $apiRequest->getVersionType();
    }

    /**
     * Missing or empty required fields never reach validation: parse() assigns
     * null to a non-nullable typed property, causing a TypeError.
     *
     * @param array<mixed> $data
     */
    #[DataProvider('provideInvalidPayloads')]
    public function testInvalidRequiredFieldsThrowTypeErrorDuringParsing(array $data): void
    {
        $this->expectException(TypeError::class);

        new CreateMetadataVersionApiRequest(self::createJsonRequest($data));
    }

    /** @return array<string, array{array<mixed>}> */
    public static function provideInvalidPayloads(): array
    {
        return [
            'missing versionType' => [['model' => 'model-id', 'modelVersion' => 'version-id']],
            'empty versionType' => [['versionType' => '', 'model' => 'model-id', 'modelVersion' => 'version-id']],
            'missing model' => [['versionType' => 'major', 'modelVersion' => 'version-id']],
            'missing modelVersion' => [['versionType' => 'major', 'model' => 'model-id']],
        ];
    }
}

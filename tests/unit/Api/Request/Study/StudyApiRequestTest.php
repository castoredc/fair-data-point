<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Study;

use App\Api\Request\Study\StudyApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;

final class StudyApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidRequest(): void
    {
        $apiRequest = new StudyApiRequest(
            self::createJsonRequest(
                [
                    'source' => 'castor',
                    'sourceServer' => 3,
                    'sourceId' => 'ABC-123',
                    'name' => 'My Study',
                    'catalog' => 'my-catalog',
                    'published' => true,
                    'slug' => 'my-study',
                ]
            )
        );

        self::assertTrue($apiRequest->getSource()->isCastor());
        self::assertSame(3, $apiRequest->getSourceServer());
        self::assertSame('ABC-123', $apiRequest->getSourceId());
        self::assertSame('My Study', $apiRequest->getName());
        self::assertSame('my-catalog', $apiRequest->getCatalog());
        self::assertTrue($apiRequest->getPublished());
        self::assertSame('my-study', $apiRequest->getSlug());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testCanProcessMinimalRequest(): void
    {
        $apiRequest = new StudyApiRequest(
            self::createJsonRequest(
                [
                    'source' => 'other',
                    'name' => 'My Study',
                ]
            )
        );

        self::assertTrue($apiRequest->getSource()->isOther());
        self::assertNull($apiRequest->getSourceServer());
        self::assertNull($apiRequest->getSourceId());
        self::assertNull($apiRequest->getCatalog());
        self::assertFalse($apiRequest->getPublished());
        self::assertNull($apiRequest->getSlug());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testSourceServerIsCastToInteger(): void
    {
        $apiRequest = new StudyApiRequest(
            self::createJsonRequest(
                [
                    'source' => 'castor',
                    'name' => 'My Study',
                    'sourceServer' => '42',
                ]
            )
        );

        self::assertSame(42, $apiRequest->getSourceServer());
    }

    public function testPublishedDefaultsToFalseWhenNull(): void
    {
        $apiRequest = new StudyApiRequest(
            self::createJsonRequest(
                [
                    'source' => 'castor',
                    'name' => 'My Study',
                    'published' => null,
                ]
            )
        );

        self::assertFalse($apiRequest->getPublished());
    }

    /**
     * An unknown source value passes validation (only NotBlank and Type are checked);
     * the failure surfaces later as an exception when getSource() builds the enum.
     */
    public function testUnknownSourcePassesValidationButGetSourceThrows(): void
    {
        $apiRequest = new StudyApiRequest(
            self::createJsonRequest(
                [
                    'source' => 'banana',
                    'name' => 'My Study',
                ]
            )
        );

        self::assertCount(0, self::createValidator()->validate($apiRequest));

        $this->expectException(InvalidArgumentException::class);

        $apiRequest->getSource();
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

        new StudyApiRequest(self::createJsonRequest($data));
    }

    /** @return array<string, array{array<mixed>}> */
    public static function provideInvalidPayloads(): array
    {
        return [
            'missing source' => [['name' => 'My Study']],
            'empty source' => [['source' => '', 'name' => 'My Study']],
            'missing name' => [['source' => 'castor']],
            'empty name' => [['source' => 'castor', 'name' => '']],
            'whitespace name' => [['source' => 'castor', 'name' => '   ']],
            'non-string sourceId' => [['source' => 'castor', 'name' => 'My Study', 'sourceId' => 7]],
        ];
    }
}

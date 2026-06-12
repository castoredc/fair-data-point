<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Distribution;

use App\Api\Request\Distribution\DistributionApiRequest;
use App\Entity\Encryption\SensitiveDataString;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

final class DistributionApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidRdfRequest(): void
    {
        $apiRequest = new DistributionApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'rdf',
                    'slug' => 'my-distribution',
                    'defaultMetadataModel' => 'model',
                    'license' => 'CC-BY-4.0',
                    'dataModel' => 'data-model',
                    'dataModelVersion' => '1.0.0',
                    'apiUser' => 'api-user',
                    'clientId' => 'client-id',
                    'clientSecret' => 'client-secret',
                    'published' => true,
                    'cached' => true,
                    'public' => false,
                ]
            )
        );

        self::assertTrue($apiRequest->getType()->isRdf());
        self::assertSame('my-distribution', $apiRequest->getSlug());
        self::assertSame('model', $apiRequest->getDefaultMetadataModel());
        self::assertSame('CC-BY-4.0', $apiRequest->getLicense());
        self::assertSame('data-model', $apiRequest->getDataModel());
        self::assertSame('1.0.0', $apiRequest->getDataModelVersion());
        self::assertSame('api-user', $apiRequest->getApiUser());
        self::assertInstanceOf(SensitiveDataString::class, $apiRequest->getClientId());
        self::assertSame('client-id', $apiRequest->getClientId()->exposeAsString());
        self::assertInstanceOf(SensitiveDataString::class, $apiRequest->getClientSecret());
        self::assertSame('client-secret', $apiRequest->getClientSecret()->exposeAsString());
        self::assertTrue($apiRequest->getPublished());
        self::assertTrue($apiRequest->isCached());
        self::assertFalse($apiRequest->isPublic());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testOptionalCredentialsAreNullWhenMissing(): void
    {
        $apiRequest = new DistributionApiRequest(self::createJsonRequest(self::validRdfPayload()));

        self::assertNull($apiRequest->getApiUser());
        self::assertNull($apiRequest->getClientId());
        self::assertNull($apiRequest->getClientSecret());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testRdfRequestRequiresDataModelAndVersion(): void
    {
        $data = self::validRdfPayload();
        unset($data['dataModel'], $data['dataModelVersion']);

        $apiRequest = new DistributionApiRequest(self::createJsonRequest($data));
        $violations = self::createValidator()->validate($apiRequest);

        self::assertSame(['dataModel', 'dataModelVersion'], self::getViolatedPropertyPaths($violations));
    }

    /**
     * Suspected bug: parse() never reads dataDictionary and dataDictionaryVersion
     * from the payload, so a csv distribution request always fails validation on
     * those fields, even when they are provided.
     */
    public function testCsvRequestAlwaysFailsOnDataDictionaryFieldsBecauseParseIgnoresThem(): void
    {
        $apiRequest = new DistributionApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'csv',
                    'slug' => 'my-distribution',
                    'defaultMetadataModel' => 'model',
                    'license' => 'CC-BY-4.0',
                    'includeAllData' => true,
                    'dataDictionary' => 'data-dictionary',
                    'dataDictionaryVersion' => '1.0.0',
                    'published' => true,
                    'cached' => false,
                    'public' => true,
                ]
            )
        );

        self::assertNull($apiRequest->getDataDictionary());
        self::assertNull($apiRequest->getDataDictionaryVersion());

        $violations = self::createValidator()->validate($apiRequest);

        self::assertSame(['dataDictionary', 'dataDictionaryVersion'], self::getViolatedPropertyPaths($violations));
    }

    public function testCsvRequestRequiresIncludeAllData(): void
    {
        $data = self::validRdfPayload();
        $data['type'] = 'csv';
        unset($data['dataModel'], $data['dataModelVersion']);

        $apiRequest = new DistributionApiRequest(self::createJsonRequest($data));
        $violations = self::createValidator()->validate($apiRequest);

        self::assertContains('includeAllData', self::getViolatedPropertyPaths($violations));
    }

    public function testMissingSlugThrowsTypeErrorDuringParsing(): void
    {
        $data = self::validRdfPayload();
        $data['slug'] = null;

        $this->expectException(TypeError::class);

        new DistributionApiRequest(self::createJsonRequest($data));
    }

    /**
     * Suspected bug: the Choice constraint on type never gets a chance to report an
     * invalid value, because getGroupSequence() converts the raw string to a
     * DistributionType enum, which throws for unknown values during validation.
     */
    public function testValidatingUnknownTypeThrowsInsteadOfReportingViolation(): void
    {
        $data = self::validRdfPayload();
        $data['type'] = 'xml';

        $apiRequest = new DistributionApiRequest(self::createJsonRequest($data));

        $this->expectException(InvalidArgumentException::class);

        self::createValidator()->validate($apiRequest);
    }

    public function testMissingTypeThrowsTypeErrorDuringParsing(): void
    {
        $data = self::validRdfPayload();
        unset($data['type']);

        $this->expectException(TypeError::class);

        new DistributionApiRequest(self::createJsonRequest($data));
    }

    /**
     * The published, cached and public flags are run through boolval(), so missing
     * keys silently become false and their NotNull constraints can never be violated.
     */
    public function testBooleanFlagsDefaultToFalseWhenMissing(): void
    {
        $data = self::validRdfPayload();
        unset($data['published'], $data['cached'], $data['public']);

        $apiRequest = new DistributionApiRequest(self::createJsonRequest($data));

        self::assertFalse($apiRequest->getPublished());
        self::assertFalse($apiRequest->isCached());
        self::assertFalse($apiRequest->isPublic());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    /** @return array<string, mixed> */
    private static function validRdfPayload(): array
    {
        return [
            'type' => 'rdf',
            'slug' => 'my-distribution',
            'defaultMetadataModel' => 'model',
            'license' => 'CC-BY-4.0',
            'dataModel' => 'data-model',
            'dataModelVersion' => '1.0.0',
            'published' => true,
            'cached' => true,
            'public' => false,
        ];
    }
}

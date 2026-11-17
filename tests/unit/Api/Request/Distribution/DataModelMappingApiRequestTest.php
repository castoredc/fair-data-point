<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Distribution;

use App\Api\Request\Distribution\DataModelMappingApiRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DataModelMappingApiRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    public function testCanProcessValidNodeMapping(): void
    {
        $apiRequest = new DataModelMappingApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'node',
                    'node' => 'node-id',
                    'elements' => ['element-1', 'element-2'],
                    'transform' => false,
                ]
            )
        );

        self::assertTrue($apiRequest->getType()->isNode());
        self::assertSame('node-id', $apiRequest->getNode());
        self::assertSame(['element-1', 'element-2'], $apiRequest->getElements());
        self::assertFalse($apiRequest->getTransform());
        self::assertNull($apiRequest->getTransformSyntax());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testCanProcessValidNodeMappingWithTransformation(): void
    {
        $apiRequest = new DataModelMappingApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'node',
                    'node' => 'node-id',
                    'elements' => ['element-1'],
                    'transform' => true,
                    'transformSyntax' => '$$element-1$$',
                ]
            )
        );

        self::assertTrue($apiRequest->getTransform());
        self::assertSame('$$element-1$$', $apiRequest->getTransformSyntax());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testTransformingNodeMappingRequiresTransformSyntax(): void
    {
        $apiRequest = new DataModelMappingApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'node',
                    'node' => 'node-id',
                    'elements' => ['element-1'],
                    'transform' => true,
                ]
            )
        );

        $violations = self::createValidator()->validate($apiRequest);

        self::assertSame(['transformSyntax'], self::getViolatedPropertyPaths($violations));
    }

    public function testNodeMappingRequiresNodeAndElements(): void
    {
        $apiRequest = new DataModelMappingApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'node',
                    'transform' => false,
                ]
            )
        );

        $violations = self::createValidator()->validate($apiRequest);
        $paths = self::getViolatedPropertyPaths($violations);

        self::assertContains('node', $paths);
        self::assertContains('elements', $paths);
        self::assertCount(2, $paths);
    }

    public function testCanProcessValidModuleMapping(): void
    {
        $apiRequest = new DataModelMappingApiRequest(
            self::createJsonRequest(
                [
                    'type' => 'module',
                    'structureType' => 'study',
                    'module' => 'module-id',
                    'element' => 'element-id',
                ]
            )
        );

        self::assertTrue($apiRequest->getType()->isModule());
        self::assertTrue($apiRequest->getStructureType()->isStudy());
        self::assertSame('module-id', $apiRequest->getModule());
        self::assertSame('element-id', $apiRequest->getElement());

        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testModuleMappingRequiresStructureTypeModuleAndElement(): void
    {
        $apiRequest = new DataModelMappingApiRequest(self::createJsonRequest(['type' => 'module']));

        $violations = self::createValidator()->validate($apiRequest);
        $paths = self::getViolatedPropertyPaths($violations);

        self::assertContains('structureType', $paths);
        self::assertContains('module', $paths);
        self::assertContains('element', $paths);
        self::assertCount(3, $paths);
    }

    /**
     * Suspected bug: the NotBlank constraint on type never gets a chance to report a
     * missing or unknown value, because getGroupSequence() converts the raw string to
     * a DataModelMappingType enum, which throws for unknown values during validation.
     */
    public function testValidatingUnknownTypeThrowsInsteadOfReportingViolation(): void
    {
        $apiRequest = new DataModelMappingApiRequest(self::createJsonRequest(['type' => 'banana']));

        $this->expectException(InvalidArgumentException::class);

        self::createValidator()->validate($apiRequest);
    }

    public function testValidatingMissingTypeThrowsInsteadOfReportingViolation(): void
    {
        $apiRequest = new DataModelMappingApiRequest(self::createJsonRequest([]));

        $this->expectException(InvalidArgumentException::class);

        self::createValidator()->validate($apiRequest);
    }
}

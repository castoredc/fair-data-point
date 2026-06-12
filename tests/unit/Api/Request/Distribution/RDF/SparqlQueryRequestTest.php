<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Distribution\RDF;

use App\Api\Request\Distribution\RDF\SparqlQueryRequest;
use App\Tests\unit\Api\Request\ApiRequestTestHelpers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use TypeError;

final class SparqlQueryRequestTest extends TestCase
{
    use ApiRequestTestHelpers;

    private const QUERY = 'SELECT * WHERE { ?s ?p ?o }';

    public function testReadsQueryFromFormData(): void
    {
        $request = Request::create(
            'https://fdp.castoredc.local',
            Request::METHOD_POST,
            ['query' => self::QUERY]
        );

        $apiRequest = new SparqlQueryRequest($request);

        self::assertSame(self::QUERY, $apiRequest->getSparqlQuery());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testFallsBackToQueryStringParameter(): void
    {
        $apiRequest = new SparqlQueryRequest(self::createQueryRequest(['query' => self::QUERY]));

        self::assertSame(self::QUERY, $apiRequest->getSparqlQuery());
        self::assertCount(0, self::createValidator()->validate($apiRequest));
    }

    public function testFormDataTakesPrecedenceOverQueryString(): void
    {
        $request = Request::create(
            'https://fdp.castoredc.local?query=from-query-string',
            Request::METHOD_POST,
            ['query' => 'from-form-data']
        );

        $apiRequest = new SparqlQueryRequest($request);

        self::assertSame('from-form-data', $apiRequest->getSparqlQuery());
    }

    public function testEmptyFormValueFallsBackToQueryString(): void
    {
        $request = Request::create(
            'https://fdp.castoredc.local?query=from-query-string',
            Request::METHOD_POST,
            ['query' => '']
        );

        $apiRequest = new SparqlQueryRequest($request);

        self::assertSame('from-query-string', $apiRequest->getSparqlQuery());
    }

    public function testEmptyQueryStringValueFailsValidation(): void
    {
        $apiRequest = new SparqlQueryRequest(self::createQueryRequest(['query' => '']));

        $violations = self::createValidator()->validate($apiRequest);

        self::assertSame(['sparqlQuery'], self::getViolatedPropertyPaths($violations));
    }

    /**
     * A request without a query never reaches validation: parse() assigns null
     * to a non-nullable typed property, causing a TypeError.
     */
    public function testMissingQueryThrowsTypeErrorDuringParsing(): void
    {
        $this->expectException(TypeError::class);

        new SparqlQueryRequest(Request::create('https://fdp.castoredc.local', Request::METHOD_POST));
    }
}

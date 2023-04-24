<?php
declare(strict_types=1);

namespace App\Graph;

use App\Entity\Encryption\SensitiveDataString;
use App\Exception\SparqlHttpRequestFailed;
use EasyRdf\Exception;
use EasyRdf\Format;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use EasyRdf\Sparql\Result;
use EasyRdf\Utils;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use function count;
use function mb_eregi;
use function sprintf;
use function strpos;
use function strtoupper;

/**
 * Class for making SPARQL queries, based on EasyRdf's Client
 * This client uses Guzzle instead of Zend and supports authentication
 */
class SparqlClient
{
    private const REGEX = '(?:(?:\s*BASE\s*<.*?>\s*)|(?:\s*PREFIX\s+.+:\s*<.*?>\s*))*(CONSTRUCT|SELECT|ASK|DESCRIBE)[\W]';
    private const RESULT_TYPES = ['application/sparql-results+json' => 1.0];

    private GuzzleHttpClient $client;
    private string $queryUri;
    private ?SensitiveDataString $user = null;
    private ?SensitiveDataString $pass = null;

    public function __construct(string $queryUri, ?SensitiveDataString $user, ?SensitiveDataString $pass)
    {
        $this->queryUri = $queryUri;
        $this->user = $user;
        $this->pass = $pass;

        $this->client = new GuzzleHttpClient(['base_url' => $queryUri]);
    }

    protected function preprocessQuery(string $query): string
    {
        // Check for undefined prefixes
        $prefixes = '';
        foreach (RdfNamespace::namespaces() as $prefix => $uri) {
            if (
                strpos($query, sprintf('{%s}:', $prefix)) !== false &&
                strpos($query, sprintf('PREFIX {%s}:', $prefix)) === false
            ) {
                $prefixes .= sprintf("PREFIX {%s}: <{%s}>\n", $prefix, $uri);
            }
        }

        return $prefixes . $query;
    }

    /**
     * @throws SparqlHttpRequestFailed
     * @throws Exception
     */
    protected function request(string $query): SparqlResponse
    {
        $processedQuery = $this->preprocessQuery($query);

        $result = null;
        $matched = mb_eregi(self::REGEX, $processedQuery, $result);

        /** @phpstan-ignore-next-line */
        if ($matched === false || count($result) !== 2) {
            // non-standard query. is this something non-standard?
            $queryVerb = null;
        } else {
            $queryVerb = strtoupper($result[1]);
        }

        if ($queryVerb === 'SELECT' || $queryVerb === 'ASK') {
            // only "results"
            $accept = Format::formatAcceptHeader(self::RESULT_TYPES);
        } elseif ($queryVerb === 'CONSTRUCT' || $queryVerb === 'DESCRIBE') {
            // only "graph"
            $accept = Format::getHttpAcceptHeader();
        } else {
            // both
            $accept = Format::getHttpAcceptHeader(self::RESULT_TYPES);
        }

        try {
            $options = [
                'headers' => ['Accept' => $accept],
                'form_params' => ['query' => $processedQuery],
            ];

            if ($this->hasUsernameAndPassword()) {
                $options['auth'] = [
                    $this->user->exposeAsString(),
                    $this->pass->exposeAsString(),
                ];
            }

            $response = $this->client->request(
                'POST',
                $this->queryUri,
                $options
            );

            return $this->parseResponseToQuery($response);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null;
            $contentType = $e->hasResponse() ? $e->getResponse()->getHeaderLine('Content-Type') : null;

            switch ($e->getCode()) {
                case 204:
                    // No content
                    return new SparqlResponse(
                        $response,
                        $contentType,
                        $this->queryUri
                    );

                default:
                    throw new SparqlHttpRequestFailed($response);
            }
        }
    }

    protected function hasUsernameAndPassword(): bool
    {
        return $this->user !== null && $this->pass !== null;
    }

    /** @throws Exception */
    protected function parseResponseToQuery(ResponseInterface $response): SparqlResponse
    {
        [$contentType] = Utils::parseMimeType($response->getHeaderLine('Content-Type'));

        $contents = $response->getBody()->getContents();

        $parsed = new SparqlResponse(
            $contents,
            $contentType,
            $this->queryUri
        );

        if (strpos($contentType, 'application/sparql-results') === 0) {
            $parsed->setResult(new Result($contents, $contentType));
        } else {
            $parsed->setGraph(new Graph($this->queryUri, $contents, $contentType));
        }

        return $parsed;
    }

    /**
     * Make a query to the SPARQL endpoint
     *
     * SELECT and ASK queries will return an object of type
     * EasyRdf\Sparql\Result.
     *
     * CONSTRUCT and DESCRIBE queries will return an object
     * of type EasyRdf\Graph.
     *
     * @param string $query The query string to be executed
     *
     * @throws SparqlHttpRequestFailed
     */
    public function query(string $query): SparqlResponse
    {
        return $this->request($query);
    }
}

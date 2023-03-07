<?php
declare(strict_types=1);

namespace App\Graph;

use App\Exception\SparqlResultsNotCompatible;
use EasyRdf\Literal;
use EasyRdf\Resource;
use function array_unique;
use function assert;
use function count;
use function json_encode;

class FederatedSparqlResponse
{
    /** @var string[] */
    private array $responses;

    /** @var string[] */
    private array $contentTypes;

    private FederatedQueryResult $result;

    public function __construct()
    {
        $this->result = new FederatedQueryResult();
    }

    /** @return string[] */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /** @param string[] $responses */
    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
    }

    /** @return string[] */
    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    public function getResult(): FederatedQueryResult
    {
        return $this->result;
    }

    public function getResultCount(): int
    {
        return $this->result->count();
    }

    /** @throws SparqlResultsNotCompatible */
    public function addSparqlResponse(SparqlResponse $response): void
    {
        $result = $response->getResult();

        if ($result === null) {
            exit;
        }

        $this->result->addResult($result);

        $this->contentTypes[] = $response->getContentType();
        $this->responses[] = $response->getResponse();
    }

    public function getContentType(): string
    {
        return 'application/sparql-results+json';
    }

    public function getResponse(): string
    {
        if ($this->result->getType() === FederatedQueryResult::TYPE_BOOLEAN) {
            $response = [
                'head' => [],
                'boolean' => count(array_unique($this->result->getResults())) === 1 && $this->result->getResults()[0] === true,
            ];
        } elseif ($this->result->getType() === FederatedQueryResult::TYPE_BINDINGS) {
            $fields = $this->result->getFields();
            $bindings = [];

            foreach ($this->result as $row) {
                $binding = [];

                foreach ($fields as $field) {
                    if (isset($row[$field])) {
                        $fieldValue = $row[$field];
                        assert($fieldValue instanceof Resource || $fieldValue instanceof Literal);

                        $binding[] = $fieldValue->toRdfPhp();
                    }
                }

                $bindings[] = $binding;
            }

            $response = [
                'head' => ['vars' => $fields],
                'results' => ['bindings' => $bindings],
            ];
        } else {
            $response = [];
        }

        $json = json_encode($response);

        return $json !== false ? $json : '';
    }
}

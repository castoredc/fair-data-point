<?php
declare(strict_types=1);

namespace App\Graph;

use App\Exception\SparqlResultsNotCompatible;
use ArrayIterator;
use Countable;
use EasyRdf\Sparql\Result;
use IteratorAggregate;
use function array_diff;
use function array_merge;
use function count;

class FederatedQueryResult implements Countable, IteratorAggregate
{
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_BINDINGS = 'bindings';

    /** @var string[] */
    private array $fields;

    private string $type;

    /** @var mixed[] */
    private array $results;

    /** @throws SparqlResultsNotCompatible */
    public function addResult(Result $result): void
    {
        // Check if fields and type are compatible
        $this->parseFields($result);
        $this->parseType($result);

        if ($result->getType() === self::TYPE_BOOLEAN) {
            $this->results[] = $result->getBoolean();
        } elseif ($result->getType() === self::TYPE_BINDINGS) {
            $this->results = array_merge($this->results, $result->getArrayCopy());
        } else {
            throw new SparqlResultsNotCompatible();
        }
    }

    /** @return string[] */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * ASK queries return a result of type 'boolean'.
     * SELECT query return a result of type 'bindings'.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /** @throws SparqlResultsNotCompatible */
    private function parseFields(Result $result): void
    {
        $fields = $result->getFields();

        if (! isset($this->fields)) {
            $this->fields = $fields;
        }

        if (
            count($this->fields) !== count($fields)
            || array_diff($this->fields, $fields) !== array_diff($fields, $this->fields)
        ) {
            throw new SparqlResultsNotCompatible();
        }
    }

    /** @throws SparqlResultsNotCompatible */
    private function parseType(Result $result): void
    {
        $type = $result->getType();

        if (! isset($this->type)) {
            $this->type = $type;
        }

        if ($this->type !== $type) {
            throw new SparqlResultsNotCompatible();
        }
    }

    public function count(): int
    {
        return count($this->results);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->results);
    }

    /** @return mixed[] */
    public function getResults(): array
    {
        return $this->results;
    }
}

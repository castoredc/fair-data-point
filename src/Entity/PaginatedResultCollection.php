<?php

namespace App\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class PaginatedResultCollection implements Countable, IteratorAggregate
{
    /** @var mixed[] */
    private $results;

    /** @var int */
    private $currentPage;

    /** @var int */
    private $perPage;

    /** @var int */
    private $totalResults;

    /**
     * @param mixed[] $results
     */
    public function __construct(array $results, int $currentPage, int $perPage, int $totalResults)
    {
        $this->results = $results;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->totalResults = $totalResults;
    }

    /**
     * @return mixed[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getStart(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalResults / $this->perPage);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->results);
    }

    /**
     * Count elements of an object
     *
     * @link  https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->results);
    }
}
<?php
declare(strict_types=1);

namespace App\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use function ceil;
use function count;

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

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getStart(): int
    {
        return ($this->currentPage - 1) * $this->perPage + 1;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function getTotalPages(): int
    {
        return $this->totalResults > 0 ? (int) ceil($this->totalResults / $this->perPage) : 0;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->results);
    }

    /**
     * Count elements of an object
     *
     * @link  https://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        return count($this->results);
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use function array_key_exists;

abstract class ApiRequest
{
    /** @var array<mixed> */
    protected $data = [];

    /** @var ParameterBag<mixed> */
    protected $query;

    /**
     * phpcs:disable
     *
     * @param array<mixed> $data
     * @param ParameterBag<mixed> $query
     *
     * phpcs:enable
     */
    public function __construct(?array $data, ParameterBag $query)
    {
        $this->data = $data ?? [];
        $this->query = $query;

        $this->parse();
    }

    abstract protected function parse(): void;

    /**
     * @return mixed|null
     */
    protected function getFromData(string $key)
    {
        if (! array_key_exists($key, $this->data)) {
            return null;
        }

        return $this->data[$key];
    }

    /**
     * @return mixed|null
     */
    protected function getFromQuery(string $key)
    {
        return $this->query->get($key);
    }
}

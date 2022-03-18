<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use function array_key_exists;
use function is_string;
use function trim;

abstract class ApiRequest
{
    /** @var array<mixed> */
    protected array $data = [];

    /** @var ParameterBag<mixed> */
    protected ParameterBag $query;

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

    /** @return mixed|null */
    protected function getFromData(string $key)
    {
        if (! array_key_exists($key, $this->data)) {
            return null;
        }

        return $this->parseValue($this->data[$key]);
    }

    /** @return mixed|null */
    protected function getFromNestedData(string $group, string $key)
    {
        if (! array_key_exists($group, $this->data) || ! array_key_exists($key, $this->data[$group])) {
            return null;
        }

        return $this->parseValue($this->data[$group][$key]);
    }

    /**
     * @param mixed|null $value
     *
     * @return mixed|null
     */
    private function parseValue($value)
    {
        if (is_string($value)) {
            $value = trim($value);

            return $value !== '' ? $value : null;
        }

        return $value;
    }

    /** @return mixed|null */
    protected function getFromQuery(string $key)
    {
        return $this->query->get($key);
    }
}

<?php

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiRequest
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var ParameterBag
     */
    protected $query;

    public function __construct(array $data, ParameterBag $query)
    {
        $this->data = $data;
        $this->query = $query;

        $this->parse();
    }

    abstract protected function parse(): void;

    /**
     * @return mixed|null
     */
    protected function getFromData(string $key)
    {
        if (!array_key_exists($key, $this->data)) {
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

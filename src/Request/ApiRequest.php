<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiRequest
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var ParameterBag
     */
    private $query;

    public function __construct(Request $request)
    {
        $data = $request->getContent();
        $this->query = $request->query;

        if (!empty($data)) {
            $this->data = json_decode($data, true);
        }

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

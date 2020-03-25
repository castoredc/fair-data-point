<?php

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class GroupedApiRequest extends ApiRequest
{
    public function __construct(Request $request, int $index)
    {
        $data = $request->getContent();
        $this->query = $request->query;

        if (!empty($data)) {
            $data = json_decode($data, true);
            $data = (array) $data[$index];
        }

        parent::__construct($data, $this->query);
    }
}

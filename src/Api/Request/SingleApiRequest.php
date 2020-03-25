<?php

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class SingleApiRequest extends ApiRequest
{
    public function __construct(Request $request)
    {
        $data = $request->getContent();
        $this->query = $request->query;

        if (!empty($data)) {
            $data = json_decode($data, true);
        }

        parent::__construct($data, $request->query);
    }
}

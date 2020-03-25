<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\Request;
use function json_decode;

abstract class SingleApiRequest extends ApiRequest
{
    public function __construct(Request $request)
    {
        $data = $request->getContent();
        $this->query = $request->query;
        $data = json_decode($data, true);

        parent::__construct($data, $request->query);
    }
}

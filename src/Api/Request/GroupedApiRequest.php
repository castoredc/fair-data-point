<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\Request;
use function json_decode;

abstract class GroupedApiRequest extends ApiRequest
{
    public function __construct(Request $request, int $index)
    {
        $data = $request->getContent();
        $this->query = $request->query;
        $data = json_decode($data, true);
        $data = (array) $data[$index];

        parent::__construct($data, $this->query);
    }
}

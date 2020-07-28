<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\HttpFoundation\Request;

abstract class SingleFormApiRequest extends ApiRequest
{
    public function __construct(Request $request)
    {
        parent::__construct(null, $request->request);
    }
}

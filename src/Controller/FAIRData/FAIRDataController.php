<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use function in_array;

abstract class FAIRDataController extends AbstractController
{
    protected function acceptsHttp(Request $request): bool
    {
        if ($request->get('format') !== null) {
            return $request->get('format') === 'html';
        }

        return in_array('text/html', $request->getAcceptableContentTypes(), true);
    }

    protected function acceptsTurtle(Request $request): bool
    {
        if ($request->get('format') !== null) {
            return $request->get('format') === 'ttl';
        }

        return in_array('text/turtle', $request->getAcceptableContentTypes(), true) || in_array('text/turtle;q=0.8', $request->getAcceptableContentTypes(), true);
    }
}

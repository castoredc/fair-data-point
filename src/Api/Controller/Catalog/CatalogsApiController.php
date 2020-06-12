<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Controller\Api\ApiController;
use App\Message\Catalog\GetCatalogsCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/catalog")
 */
class CatalogsApiController extends ApiController
{
    /**
     * @Route("", name="api_catalogs")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCatalogsCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

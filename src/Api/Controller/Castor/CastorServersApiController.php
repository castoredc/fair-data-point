<?php
declare(strict_types=1);

namespace App\Api\Controller\Castor;

use App\Api\Resource\Security\CastorServersApiResource;
use App\Controller\Api\ApiController;
use App\Message\Security\GetCastorServersCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CastorServersApiController extends ApiController
{
    /**
     * @Route("/api/castor/servers", name="api_servers")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCastorServersCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new CastorServersApiResource($handledStamp->getResult()))->toArray());
    }
}

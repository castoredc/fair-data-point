<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Resource\Security\CastorServersApiResource;
use App\Message\Security\GetCastorServersCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CastorServersApiController extends ApiController
{
    /**
     * @Route("/api/servers", name="api_servers")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCastorServersCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new CastorServersApiResource($handledStamp->getResult()))->toArray());
    }
}

<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Resource\FAIRDataPoint\FAIRDataPointApiResource;
use App\Message\FAIRDataPoint\GetFAIRDataPointCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class FAIRDataPointApiController extends ApiController
{
    /**
     * @Route("/api/fdp", name="api_fdp")
     */
    public function fdp(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetFAIRDataPointCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new FAIRDataPointApiResource($handledStamp->getResult()))->toArray());
    }
}

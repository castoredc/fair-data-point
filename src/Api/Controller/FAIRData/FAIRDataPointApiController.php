<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Api\Resource\FAIRDataPoint\FAIRDataPointApiResource;
use App\Command\FAIRDataPoint\GetFAIRDataPointCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class FAIRDataPointApiController extends ApiController
{
    /** @Route("/api/fdp", name="api_fdp") */
    public function fdp(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetFAIRDataPointCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse((new FAIRDataPointApiResource($handledStamp->getResult()))->toArray());
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Message\Language\GetLanguagesCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class LanguagesApiController extends ApiController
{
    /**
     * @Route("/api/languages", name="api_languages")
     */
    public function countries(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetLanguagesCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

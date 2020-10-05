<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Message\Country\GetCountriesCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class CountriesApiController extends ApiController
{
    /**
     * @Route("/api/countries", name="api_countries")
     */
    public function countries(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCountriesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Api\Resource\Country\CountryApiResource;
use App\Command\Country\GetCountriesCommand;
use App\Entity\FAIRData\Country;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class CountriesApiController extends ApiController
{
    #[Route(path: '/api/countries', name: 'api_countries')]
    public function countries(MessageBusInterface $bus): Response
    {
        $envelope = $this->bus->dispatch(new GetCountriesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }

    #[Route(path: '/api/country/{code}', name: 'api_country')]
    public function language(
        #[MapEntity(mapping: ['code' => 'code'])]
        Country $country,
    ): Response {
        return new JsonResponse((new CountryApiResource($country))->toArray());
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Api\Resource\Language\LanguageApiResource;
use App\Command\Language\GetLanguagesCommand;
use App\Entity\FAIRData\Language;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class LanguagesApiController extends ApiController
{
    #[Route(path: '/api/languages', name: 'api_languages')]
    public function languages(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetLanguagesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }

    #[Route(path: '/api/language/{code}', name: 'api_language')]
    public function language(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['code' => 'code'])]
    Language $language): Response
    {
        return new JsonResponse((new LanguageApiResource($language))->toArray());
    }
}

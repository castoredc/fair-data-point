<?php
declare(strict_types=1);

namespace App\Api\Controller\Data\DataDictionary;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataDictionary\DataDictionaryApiRequest;
use App\Api\Resource\Data\DataDictionary\DataDictionariesApiResource;
use App\Api\Resource\Data\DataDictionary\DataDictionaryApiResource;
use App\Command\Data\DataDictionary\CreateDataDictionaryCommand;
use App\Command\Data\DataDictionary\GetDataDictionariesCommand;
use App\Exception\ApiRequestParseError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/dictionary")
 */
class DataDictionariesApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_dictionaries")
     */
    public function dataDictionaries(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $envelope = $bus->dispatch(new GetDataDictionariesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse((new DataDictionariesApiResource($handledStamp->getResult()))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_dictionaries_add")
     */
    public function addDataDictionary(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(DataDictionaryApiRequest::class, $request);
            assert($parsed instanceof DataDictionaryApiRequest);

            $envelope = $bus->dispatch(new CreateDataDictionaryCommand($parsed->getTitle(), $parsed->getDescription()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataDictionaryApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data dictionary', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

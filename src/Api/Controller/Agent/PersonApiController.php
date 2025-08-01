<?php
declare(strict_types=1);

namespace App\Api\Controller\Agent;

use App\Api\Controller\ApiController;
use App\Api\Request\Agent\PersonApiRequest;
use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Command\Agent\GetPersonByEmailCommand;
use App\Exception\ApiRequestParseError;
use App\Exception\NotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/agent/person')]
class PersonApiController extends ApiController
{
    #[Route(path: '/email', methods: ['GET'], name: 'api_agent_person')]
    public function getPersonByEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            $parsed = $this->parseRequest(PersonApiRequest::class, $request);
            assert($parsed instanceof PersonApiRequest);
            $envelope = $this->bus->dispatch(new GetPersonByEmailCommand($parsed->getEmail()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new PersonApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NotFound) {
                return new JsonResponse([], Response::HTTP_NOT_FOUND);
            }

            $this->logger->critical('An error occurred while searching for a person', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

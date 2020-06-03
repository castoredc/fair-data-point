<?php
declare(strict_types=1);

namespace App\Api\Controller\Terminology;

use App\Api\Request\Terminology\OntologyConceptApiRequest;
use App\Api\Resource\Terminology\OntologiesApiResource;
use App\Api\Resource\Terminology\OntologyConceptSearchApiResource;
use App\Controller\Api\ApiController;
use App\Exception\ApiRequestParseError;
use App\Message\Terminology\FindOntologyConceptsCommand;
use App\Message\Terminology\GetOntologiesCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/terminology")
 */
class TerminologyApiController extends ApiController
{
    /**
     * @Route("/ontologies", name="api_terminology_ontologies")
     */
    public function ontologies(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetOntologiesCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new OntologiesApiResource($handledStamp->getResult()))->toArray());
    }

    /**
     * @Route("/concepts", name="api_terminology_concepts")
     */
    public function concepts(Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var OntologyConceptApiRequest $parsed */
            $parsed = $this->parseRequest(OntologyConceptApiRequest::class, $request);
            $envelope = $bus->dispatch(new FindOntologyConceptsCommand($parsed->getOntology(), $parsed->getSearch()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse((new OntologyConceptSearchApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Terminology;

use App\Api\Controller\ApiController;
use App\Api\Request\Terminology\OntologyConceptApiRequest;
use App\Api\Resource\Terminology\OntologiesApiResource;
use App\Api\Resource\Terminology\OntologyConceptSearchApiResource;
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
use function assert;

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

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse((new OntologiesApiResource($handledStamp->getResult()))->toArray());
    }

    /**
     * @Route("/concepts", name="api_terminology_concepts")
     */
    public function concepts(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $parsed = $this->parseRequest(OntologyConceptApiRequest::class, $request);
            assert($parsed instanceof OntologyConceptApiRequest);
            $envelope = $bus->dispatch(new FindOntologyConceptsCommand($parsed->getOntology(), $parsed->getSearch(), $parsed->includeIndividuals()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new OntologyConceptSearchApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting concept suggestions', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }
}

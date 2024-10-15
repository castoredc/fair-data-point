<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Api\Controller\ApiController;
use App\Api\Request\Distribution\RDF\FederatedSparqlQueryRequest;
use App\Command\Distribution\RDF\RunFederatedQueryAgainstDistributionSparqlEndpointsCommand;
use App\Exception\ApiRequestParseError;
use App\Graph\FederatedSparqlResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class FederatedSPARQLController extends ApiController
{
    #[Route(path: '/fdp/distributions/sparql', name: 'federated_sparql')]
    public function rdfDistributionSparql(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $parsed = $this->parseRequest(FederatedSparqlQueryRequest::class, $request);
            assert($parsed instanceof FederatedSparqlQueryRequest);

            $handledStamp = $bus->dispatch(
                new RunFederatedQueryAgainstDistributionSparqlEndpointsCommand(
                    $parsed->getDistributionIds(),
                    $parsed->getSparqlQuery()
                )
            )->last(HandledStamp::class);

            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();
            assert($results instanceof FederatedSparqlResponse);

            return new Response(
                $results->getResponse(),
                Response::HTTP_OK,
                [
                    'Content-Type' => $results->getContentType(),
                ]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical(
                'An error occurred while executing a federated query.',
                [
                    'exception' => $e,
                    'errorMessage' => $e->getMessage(),
                ]
            );

            return new Response('An error occurred while executing your query.', Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route(path: '/fdp/distributions/query', name: 'federated_query')]
    public function rdfDistributionQuery(): Response
    {
        return $this->render('react.html.twig');
    }
}

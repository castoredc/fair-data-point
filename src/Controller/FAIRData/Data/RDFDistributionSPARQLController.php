<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Api\Controller\ApiController;
use App\Api\Request\Distribution\RDF\SparqlQueryRequest;
use App\Command\Distribution\RDF\RunQueryAgainstDistributionSparqlEndpointCommand;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Event\SparqlQueryExecuted;
use App\Event\SparqlQueryFailed;
use App\Exception\ApiRequestParseError;
use App\Graph\SparqlResponse;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function assert;

class RDFDistributionSPARQLController extends ApiController
{
    public function __construct(
        ApiClient $apiClient,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        MessageBusInterface $bus,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($apiClient, $validator, $logger, $em, $bus);
    }

    #[Route(path: '/fdp/dataset/{dataset}/distribution/{distribution}/sparql', name: 'distribution_sparql')]
    public function rdfDistributionSparql(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        #[MapEntity(mapping: ['distribution' => 'slug'])]
        Distribution $distribution,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('access_data', $distribution);
        $contents = $distribution->getContents();

        if (
            ! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution || ! $contents->isCached(
            )
        ) {
            throw $this->createNotFoundException();
        }

        try {
            $parsed = $this->parseRequest(SparqlQueryRequest::class, $request);
            assert($parsed instanceof SparqlQueryRequest);

            $handledStamp = $this->bus->dispatch(
                new RunQueryAgainstDistributionSparqlEndpointCommand(
                    $contents,
                    $parsed->getSparqlQuery()
                )
            )->last(HandledStamp::class);

            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();
            assert($results instanceof SparqlResponse);

            $this->eventDispatcher->dispatch(
                new SparqlQueryExecuted(
                    $distribution->getId(),
                    $this->getUser(),
                    $request->get('query'),
                    $results->getResultCount()
                )
            );

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
                'An error occurred while executing a query.',
                [
                    'exception' => $e,
                    'errorMessage' => $e->getMessage(),
                    'Distribution' => $distribution->getSlug(),
                    'DistributionID' => $distribution->getId(),
                ]
            );

            $this->eventDispatcher->dispatch(
                new SparqlQueryFailed(
                    $distribution->getId(),
                    $this->getUser(),
                    $request->get('query'),
                    $e->getMessage()
                )
            );

            return new Response('An error occurred while executing your query.', Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route(path: '/fdp/dataset/{dataset}/distribution/{distribution}/query', name: 'distribution_query')]
    public function rdfDistributionQuery(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        #[MapEntity(mapping: ['distribution' => 'slug'])]
        Distribution $distribution,
    ): Response {
        $this->denyAccessUnlessGranted('access_data', $distribution);
        $contents = $distribution->getContents();

        if (
            ! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution || ! $contents->isCached(
            )
        ) {
            throw $this->createNotFoundException();
        }

        return $this->render('react.html.twig');
    }
}

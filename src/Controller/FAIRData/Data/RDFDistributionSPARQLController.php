<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Command\Distribution\RDF\GetRDFEndpointCommand;
use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Event\SparqlQueryExecuted;
use App\Event\SparqlQueryFailed;
use App\Service\UriHelper;
use ARC2_StoreEndpoint;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use function assert;
use function count;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class RDFDistributionSPARQLController extends FAIRDataController
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UriHelper $uriHelper, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($uriHelper, $logger);
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/fdp/dataset/{dataset}/distribution/{distribution}/sparql", name="distribution_sparql")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function rdfDistributionSparql(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution || ! $contents->isCached()) {
            throw $this->createNotFoundException();
        }

        $handledStamp = $bus->dispatch(new GetRDFEndpointCommand($contents))->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $endpoint = $handledStamp->getResult();
        assert($endpoint instanceof ARC2_StoreEndpoint);

        if ($request->get('query') === null) {
            return $this->redirectToRoute('distribution_query', [
                'dataset' => $dataset->getSlug(),
                'distribution' => $distribution->getSlug(),
            ]);
        }

        try {
            $endpoint->handleRequest();
            $endpoint->sendHeaders();

            $rawResults = $endpoint->getResult();
            $parsedResults = json_decode($rawResults, true, 512, JSON_THROW_ON_ERROR);

            $this->eventDispatcher->dispatch(
                new SparqlQueryExecuted(
                    $distribution->getId(),
                    $this->getUser(),
                    $request->get('query'),
                    count($parsedResults['results']['bindings']) ?? 0
                )
            );

            echo $rawResults;

            exit;
        } catch (Throwable $t) {
            $this->logger->critical('An error occurred while executing a query.', [
                'exception' => $t,
                'errorMessage' => $t->getMessage(),
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            $this->eventDispatcher->dispatch(
                new SparqlQueryFailed(
                    $distribution->getId(),
                    $this->getUser(),
                    $request->get('query'),
                    $t->getMessage()
                )
            );

            return new Response('An error occurred while executing your query.', 400);
        }
    }

    /**
     * @Route("/fdp/dataset/{dataset}/distribution/{distribution}/query", name="distribution_query")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function rdfDistributionQuery(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution || ! $contents->isCached()) {
            throw $this->createNotFoundException();
        }

        return $this->render('react.html.twig');
    }
}

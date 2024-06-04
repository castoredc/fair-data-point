<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Command\Distribution\GetRecordCommand;
use App\Command\Distribution\GetRecordsCommand;
use App\Command\Distribution\RDF\GetRDFFromStoreCommand;
use App\Command\Distribution\RDF\RenderRDFDistributionCommand;
use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use EasyRdf\Graph;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function urlencode;

class RDFDistributionController extends FAIRDataController
{
    /**
     * @Route("/fdp/dataset/{dataset}/distribution/{distribution}/rdf", name="distribution_rdf")
     * @Route("/fdp/dataset/{dataset}/distribution/{distribution}/rdf/{record}", name="distribution_rdf_record")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function rdfDistribution(Dataset $dataset, Distribution $distribution, ?string $record, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $distribution);

        $contents = $distribution->getContents();
        assert($contents instanceof RDFDistribution);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        try {
            if ($contents->isCached()) {
                $handledStamp = $bus->dispatch(new GetRDFFromStoreCommand($contents, $record))->last(HandledStamp::class);
                assert($handledStamp instanceof HandledStamp);

                $turtle = $handledStamp->getResult();
            } else {
                if ($record !== null) {
                    $handledStamp = $bus->dispatch(new GetRecordCommand($distribution, $record))->last(HandledStamp::class);
                    assert($handledStamp instanceof HandledStamp);
                    $records = [$handledStamp->getResult()];
                } else {
                    $handledStamp = $bus->dispatch(new GetRecordsCommand($distribution))->last(HandledStamp::class);
                    assert($handledStamp instanceof HandledStamp);
                    $records = $handledStamp->getResult();
                }

                $handledStamp = $bus->dispatch(new RenderRDFDistributionCommand($records, $contents))->last(HandledStamp::class);
                assert($handledStamp instanceof HandledStamp);

                $graph = $handledStamp->getResult();
                assert($graph instanceof Graph);
                $turtle = $graph->serialise('turtle');
            }
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return $this->redirectToRoute(
                    'login',
                    [
                        'path' => urlencode($request->getUri()),
                        'session_expired' => true,
                        'dataset' => true,
                    ]
                );
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            $this->logger->critical('An error occurred while loading the RDF for a distribution', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse(['error' => 'An error occurred while loading the RDF for a distribution.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getTurtleResponse($distribution, $turtle, $request->query->getBoolean('download'));
    }

    /** @Route("/fdp/dataset/{dataset}/distribution/{distribution}/rdf/{record}/{element}", name="distribution_rdf_record_element") */
    public function rdfDistributionElement(string $dataset, string $distribution, string $record, string $element): Response
    {
        return $this->redirectToRoute('distribution_rdf_record', [
            'dataset' => $dataset,
            'distribution' => $distribution,
            'record' => $record,
        ]);
    }
}

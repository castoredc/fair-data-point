<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\GetRecordCommand;
use App\Message\Distribution\GetRecordsCommand;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Security\CastorUser;
use EasyRdf_Graph;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function dump;
use function time;

class RDFDistributionController extends FAIRDataController
{
    /**
     * @Route("/fdp/dataset/{dataset}/{distribution}/rdf", name="distribution_rdf")
     * @Route("/fdp/dataset/{dataset}/{distribution}/rdf/{record}", name="distribution_rdf_record")
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function rdfDistribution(Dataset $dataset, Distribution $distribution, ?string $record, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution) {
            throw $this->createNotFoundException();
        }

        $study = $distribution->getDataset()->getStudy();
        assert($study instanceof CastorStudy);

        /** @var CastorUser|null $user */
        $user = $this->getUser();

        try {
            if ($record !== null) {
                // Get specific record

                /** @var HandledStamp $handledStamp */
                $handledStamp = $bus->dispatch(new GetRecordCommand($study, $record))->last(HandledStamp::class);

                /** @var Record $record */
                $record = $handledStamp->getResult();
                $records = [$record];
            } else {
                /** @var HandledStamp $handledStamp */
                $handledStamp = $bus->dispatch(new GetRecordsCommand($study))->last(HandledStamp::class);

                /** @var Record[] $records */
                $records = $handledStamp->getResult();
            }

            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new RenderRDFDistributionCommand($records, $contents))->last(HandledStamp::class);

            /** @var EasyRdf_Graph $graph */
            $graph = $handledStamp->getResult();

            if ($request->query->getBoolean('download') === true) {
                $response = new Response($graph->serialise('turtle'));
                $disposition = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $dataset->getStudy()->getSlug() . '_' . time() . '.ttl'
                );
                $response->headers->set('Content-Disposition', $disposition);

                return $response;
            }

            return new Response(
                $graph->serialise('turtle'),
                Response::HTTP_OK,
                ['content-type' => 'text/turtle']
            );
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), 401);
            }
            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }
            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), 403);
            }

            dump($e);

            return new JsonResponse([], 500);
        }
    }
}

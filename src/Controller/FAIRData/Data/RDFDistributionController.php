<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Castor\Record;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\FAIRData\Distribution\RDFDistribution\RDFDistribution;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\GetRecordCommand;
use App\Message\Distribution\GetRecordsCommand;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function time;

class RDFDistributionController extends FAIRDataController
{
    /**
     * @Route("/fdp/{catalog}/{dataset}/{distribution}/rdf", name="distribution_rdf")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function rdfDistribution(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $dataset->getStudy());

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $distribution instanceof RDFDistribution) {
            throw $this->createNotFoundException();
        }

        /** @var CastorUser|null $user */
        $user = $this->getUser();

        try {
            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new GetRecordsCommand($dataset->getStudy(), $user))->last(HandledStamp::class);

            /** @var Record[] $records */
            $records = $handledStamp->getResult();

            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new RenderRDFDistributionCommand($records, $distribution, $user))->last(HandledStamp::class);
            $turtle = $handledStamp->getResult();

            if ($request->query->getBoolean('download') === true) {
                $response = new Response($turtle);
                $disposition = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $dataset->getStudy()->getSlug() . '_' . time() . '.ttl'
                );
                $response->headers->set('Content-Disposition', $disposition);

                return $response;
            }

            return new Response(
                $turtle,
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

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/fdp/{catalog}/{dataset}/{distribution}/rdf/{record}", name="distribution_rdf_record")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function rdfRecordDistribution(Catalog $catalog, Dataset $dataset, Distribution $distribution, string $record, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $dataset->getStudy());

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $distribution instanceof RDFDistribution) {
            throw $this->createNotFoundException();
        }

        /** @var CastorUser|null $user */
        $user = $this->getUser();

        try {
            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new GetRecordCommand($dataset->getStudy(), $record, $user))->last(HandledStamp::class);

            /** @var Record[] $record */
            $record = $handledStamp->getResult();

            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new RenderRDFDistributionCommand($record, $distribution, $user))->last(HandledStamp::class);
            $turtle = $handledStamp->getResult();

            if ($request->query->getBoolean('download') === true) {
                $response = new Response($turtle);
                $disposition = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $dataset->getStudy()->getSlug() . '_' . time() . '.ttl'
                );
                $response->headers->set('Content-Disposition', $disposition);

                return $response;
            }

            return new Response(
                $turtle,
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

            return new JsonResponse([], 500);
        }
    }
}

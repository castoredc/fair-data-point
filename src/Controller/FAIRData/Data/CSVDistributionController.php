<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Command\Distribution\CSV\RenderCSVDistributionCommand;
use App\Command\Distribution\GetRecordsCommand;
use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function time;

class CSVDistributionController extends FAIRDataController
{
    #[Route(path: '/fdp/{catalog}/{dataset}/{distribution}/distribution/csv', name: 'distribution_csv')]
    public function csvDistribution(#[MapEntity(mapping: ['catalog' => 'slug'])]
    Catalog $catalog, #[MapEntity(mapping: ['dataset' => 'slug'])]
    Dataset $dataset, #[MapEntity(mapping: ['distribution' => 'slug'])]
    Distribution $distribution, MessageBusInterface $bus,): Response
    {
        $this->denyAccessUnlessGranted('access_data', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $contents instanceof CSVDistribution) {
            throw $this->createNotFoundException();
        }

        $study = $distribution->getDataset()->getStudy();
        assert($study instanceof CastorStudy);

        try {
            $handledStamp = $bus->dispatch(new GetRecordsCommand($distribution))->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            /** @var Record[] $records */
            $records = $handledStamp->getResult();

            $handledStamp = $bus->dispatch(new RenderCSVDistributionCommand($records, $contents, $catalog))->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);
            $csv = $handledStamp->getResult();

            $response = new Response($csv);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $dataset->getStudy()->getSlug() . '_' . time() . '.csv'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

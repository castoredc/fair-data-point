<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Castor\Record;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\FAIRData\Distribution;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\GetRecordsCommand;
use App\Message\Distribution\RenderCSVDistributionCommand;
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

class CSVDistributionController extends FAIRDataController
{
    /**
     * @Route("/fdp/{catalog}/{dataset}/{distribution}/csv", name="distribution_csv")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function csvDistribution(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('access_data', $distribution);

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $distribution instanceof CSVDistribution) {
            throw $this->createNotFoundException();
        }

        /** @var CastorUser|null $user */
        $user = $this->getUser();

        try {
            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new GetRecordsCommand($distribution, $catalog, $user))->last(HandledStamp::class);

            /** @var Record[] $records */
            $records = $handledStamp->getResult();

            /** @var HandledStamp $handledStamp */
            $handledStamp = $bus->dispatch(new RenderCSVDistributionCommand($records, $distribution, $catalog, $user))->last(HandledStamp::class);
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
                return new JsonResponse($e->toArray(), 401);
            }
            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }

            return new JsonResponse([], 500);
        }
    }
}

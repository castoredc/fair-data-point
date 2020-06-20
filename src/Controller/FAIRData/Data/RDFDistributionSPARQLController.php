<?php
declare(strict_types=1);

namespace App\Controller\FAIRData\Data;

use App\Controller\FAIRData\FAIRDataController;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Message\Distribution\GetRDFEndpointCommand;
use ARC2_StoreEndpoint;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class RDFDistributionSPARQLController extends FAIRDataController
{
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

        /** @var HandledStamp $handledStamp */
        $handledStamp = $bus->dispatch(new GetRDFEndpointCommand($contents))->last(HandledStamp::class);

        /** @var ARC2_StoreEndpoint $endpoint */
        $endpoint = $handledStamp->getResult();

        $endpoint->go();
        exit;
    }
}

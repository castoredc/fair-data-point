<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class DistributionController extends FAIRDataController
{
    #[Route(path: '/fdp/dataset/{dataset}/distribution/{distribution}', name: 'distribution')]
    public function distribution(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['dataset' => 'slug'])]
    Dataset $dataset, #[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['distribution' => 'slug'])]
    Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset->getStudy());

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return $this->renderResource(
            $request,
            $distribution,
            $bus
        );
    }
}

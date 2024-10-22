<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Dataset;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DatasetController extends FAIRDataController
{
    #[Route(path: '/fdp/dataset/{dataset}', name: 'dataset')]
    public function dataset(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataset);

        return $this->renderResource(
            $request,
            $dataset
        );
    }
}

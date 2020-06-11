<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\Castor\Study;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudyController extends FAIRDataController
{
    /**
     * @Route("/study/{study}", name="study")
     * @ParamConverter("study", options={"mapping": {"study": "slug"}})
     */
    public function study(Study $study): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        if (! $study->hasMetadata()) {
            throw $this->createNotFoundException('This study cannot be found');
        }

        $metadata = $study->getLatestMetadata();

        return $this->render(
            'react.html.twig',
            [
                'title' => $metadata->getBriefName(),
                'description' => $metadata->getBriefSummary(),
            ],
        );
    }
}

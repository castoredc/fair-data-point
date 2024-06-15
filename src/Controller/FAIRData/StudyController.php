<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\Study;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class StudyController extends FAIRDataController
{
    /**
     * @Route("/study/{study}", name="study")
     * @ParamConverter("study", options={"mapping": {"study": "slug"}})
     */
    public function study(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        return $this->renderResource(
            $request,
            $study,
            $bus
        );
    }
}

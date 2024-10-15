<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\Study;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class StudyController extends FAIRDataController
{
    #[Route(path: '/study/{study}', name: 'study')]
    public function study(#[MapEntity(mapping: ['study' => 'slug'])]
    Study $study, Request $request, MessageBusInterface $bus,): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        return $this->renderResource(
            $request,
            $study,
            $bus
        );
    }
}

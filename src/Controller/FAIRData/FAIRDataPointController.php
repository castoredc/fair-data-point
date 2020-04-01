<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\FAIRDataPoint;
use App\Message\FAIRDataPoint\GetFAIRDataPointCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class FAIRDataPointController extends FAIRDataController
{
    /**
     * @Route("/fdp", name="fdp")
     */
    public function index(Request $request, MessageBusInterface $bus): Response
    {
        if ($this->acceptsHttp($request)) {
            return $this->render(
                'react.html.twig'
            );
        }

        $envelope = $bus->dispatch(new GetFAIRDataPointCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $handledStamp->getResult();

        return new Response(
            $fdp->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}

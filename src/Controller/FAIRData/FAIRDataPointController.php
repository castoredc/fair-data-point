<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\FAIRDataPoint;
use App\Graph\Resource\FAIRDataPoint\FAIRDataPointGraphResource;
use App\Message\FAIRDataPoint\GetFAIRDataPointCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class FAIRDataPointController extends FAIRDataController
{
    /**
     * @Route("/fdp", name="fdp")
     */
    public function index(Request $request, MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetFAIRDataPointCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $fdp = $handledStamp->getResult();
        assert($fdp instanceof FAIRDataPoint);

        if ($this->acceptsHttp($request)) {
            return $this->render('react.html.twig', $this->getSeoTexts($fdp));
        }

        return new Response(
            (new FAIRDataPointGraphResource($fdp, $this->baseUri))->toGraph()->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}

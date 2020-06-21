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

class FAIRDataPointController extends FAIRDataController
{
    /**
     * @Route("/fdp", name="fdp")
     */
    public function index(Request $request, MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetFAIRDataPointCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        /** @var FAIRDataPoint $fdp */
        $fdp = $handledStamp->getResult();

        if ($this->acceptsHttp($request)) {
            return $this->render(
                'react.html.twig',
                [
                    'title' => $fdp->getTitle()->getTextByLanguageString('en')->getText(),
                    'description' => $fdp->getDescription()->getTextByLanguageString('en')->getText(),
                ],
            );
        }

        return new Response(
            (new FAIRDataPointGraphResource($fdp))->toGraph($this->baseUri)->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}

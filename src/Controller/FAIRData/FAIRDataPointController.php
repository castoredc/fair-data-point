<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Command\FAIRDataPoint\GetFAIRDataPointCommand;
use App\Entity\FAIRData\FAIRDataPoint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class FAIRDataPointController extends FAIRDataController
{
    /** @Route("/fdp", name="fdp") */
    public function index(Request $request, MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetFAIRDataPointCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $fdp = $handledStamp->getResult();
        assert($fdp instanceof FAIRDataPoint);

        return $this->renderResource(
            $request,
            $fdp,
            $bus
        );
    }
}

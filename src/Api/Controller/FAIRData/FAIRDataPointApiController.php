<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Api\Resource\FAIRDataPoint\FAIRDataPointApiResource;
use App\Command\FAIRDataPoint\GetFAIRDataPointCommand;
use App\Security\Authorization\Voter\FAIRDataPointVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class FAIRDataPointApiController extends ApiController
{
    #[Route(path: '/api/fdp', name: 'api_fdp')]
    public function fdp(MessageBusInterface $bus): Response
    {
        $envelope = $this->bus->dispatch(new GetFAIRDataPointCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);
        $fdp = $handledStamp->getResult();

        return $this->getResponseWithAssociatedItemCount(
            new FAIRDataPointApiResource($fdp),
            $fdp,
            [FAIRDataPointVoter::VIEW, FAIRDataPointVoter::EDIT]
        );
    }
}

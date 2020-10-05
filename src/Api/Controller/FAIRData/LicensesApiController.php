<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Message\License\GetLicensesCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class LicensesApiController extends ApiController
{
    /**
     * @Route("/api/licenses", name="api_licenses")
     */
    public function countries(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetLicensesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

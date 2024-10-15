<?php
declare(strict_types=1);

namespace App\Api\Controller\Distribution;

use App\Api\Controller\ApiController;
use App\Api\Resource\Distribution\DistributionTreeApiResource;
use App\Command\Distribution\FindDistributionsByUserCommand;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class MyDistributionsApiController extends ApiController
{
    #[Route(path: '/api/distributions/tree', methods: ['GET'], name: 'api_distribution_tree')]
    public function distributions(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        assert($user instanceof User);

        try {
            $envelope = $bus->dispatch(
                new FindDistributionsByUserCommand(
                    $user
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return new JsonResponse((new DistributionTreeApiResource($results))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while getting the distribution tree view', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Message\Study\FindStudiesByUserCommand;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/study")
 */
class MyStudiesApiController extends ApiController
{
    /**
     * @Route("/my", methods={"GET"}, name="api_my_studies")
     */
    public function myStudies(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);
        $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, false));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult());
    }
}

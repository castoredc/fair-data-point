<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Resource\Study\StudyApiResource;
use App\Command\Study\FindStudiesByUserCommand;
use App\Entity\PaginatedResultCollection;
use App\Exception\SessionTimedOut;
use App\Security\Authorization\Voter\StudyVoter;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function count;

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

        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, false));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return $this->getPaginatedResponse(
                StudyApiResource::class,
                new PaginatedResultCollection(
                    $handledStamp->getResult(),
                    1,
                    count($handledStamp->getResult()),
                    count($handledStamp->getResult())
                ),
                [StudyVoter::VIEW, StudyVoter::EDIT, StudyVoter::EDIT_SOURCE_SYSTEM]
            );
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), 401);
            }

            $this->logger->critical('An error occurred while loading the studies', ['exception' => $e]);
        }

        return new JsonResponse([], 500);
    }
}

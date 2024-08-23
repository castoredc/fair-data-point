<?php
declare(strict_types=1);

namespace App\Api\Controller\Dataset;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\MetadataFilterApiRequest;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Command\Dataset\GetPaginatedDatasetsCommand;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/** @Route("/api/dataset") */
class DatasetsApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_datasets") */
    public function datasets(Request $request, MessageBusInterface $bus): Response
    {
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $parsed = $this->parseRequest(MetadataFilterApiRequest::class, $request);
            assert($parsed instanceof MetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedDatasetsCommand(
                    null,
                    null,
                    $user,
                    $parsed->getSearch(),
                    $parsed->getHideParents(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                DatasetApiResource::class,
                $results,
                [DatasetVoter::VIEW, DatasetVoter::EDIT, DatasetVoter::MANAGE]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the datasets', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

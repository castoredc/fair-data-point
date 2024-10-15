<?php
declare(strict_types=1);

namespace App\Api\Controller\Dataset;

use App\Api\Controller\ApiController;
use App\Api\Request\Dataset\DatasetApiRequest;
use App\Api\Request\Metadata\MetadataFilterApiRequest;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Command\Dataset\UpdateDatasetCommand;
use App\Command\Distribution\GetPaginatedDistributionsCommand;
use App\Entity\FAIRData\Dataset;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\Authorization\Voter\DistributionVoter;
use App\Security\User;
use App\Service\UriHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/dataset/{dataset}')]
class DatasetApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_dataset')]
    public function dataset(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataset);

        return $this->getResponse(
            new DatasetApiResource($dataset),
            $dataset,
            [DatasetVoter::VIEW, DatasetVoter::EDIT, DatasetVoter::MANAGE]
        );
    }

    #[Route(path: '', methods: ['POST'], name: 'api_dataset_update')]
    public function updateDataset(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DatasetVoter::EDIT, $dataset);

        try {
            $parsed = $this->parseRequest(DatasetApiRequest::class, $request, $dataset);
            assert($parsed instanceof DatasetApiRequest);
            $bus->dispatch(
                new UpdateDatasetCommand(
                    $dataset,
                    $parsed->getSlug(),
                    $parsed->getPublished(),
                    $parsed->getDefaultMetadataModel()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating a dataset',
                [
                    'exception' => $e,
                    'Dataset' => $dataset->getSlug(),
                    'DatasetID' => $dataset->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/distribution', methods: ['GET'], name: 'api_dataset_distributions')]
    public function distributions(
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        Request $request,
        MessageBusInterface $bus,
        UriHelper $uriHelper,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataset);
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $parsed = $this->parseRequest(MetadataFilterApiRequest::class, $request);
            assert($parsed instanceof MetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedDistributionsCommand(
                    null,
                    $dataset,
                    null,
                    $user,
                    $parsed->getSearch(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                DistributionApiResource::class,
                $results,
                [DistributionVoter::VIEW, DistributionVoter::EDIT, DistributionVoter::MANAGE, DistributionVoter::ACCESS_DATA],
                $uriHelper
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the distributions for a dataset',
                [
                    'exception' => $e,
                    'Dataset' => $dataset->getSlug(),
                    'DatasetID' => $dataset->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

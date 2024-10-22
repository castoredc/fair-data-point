<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Command\Dataset\CreateDatasetForStudyCommand;
use App\Command\Dataset\GetDatasetsByStudyCommand;
use App\Entity\Study;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\Authorization\Voter\StudyVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/study/{study}')]
class StudyDatasetApiController extends ApiController
{
    #[Route(path: '/dataset', methods: ['GET'], name: 'api_study_datasets')]
    public function datasets(
        #[MapEntity(mapping: ['study' => 'id'])]
        Study $study,
    ): Response {
        $this->denyAccessUnlessGranted('view', $study);

        $envelope = $this->bus->dispatch(new GetDatasetsByStudyCommand($study));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $results = $handledStamp->getResult();

        return $this->getPaginatedResponse(
            DatasetApiResource::class,
            $results,
            [DatasetVoter::VIEW, DatasetVoter::EDIT, DatasetVoter::MANAGE]
        );
    }

    #[Route(path: '/dataset', methods: ['POST'], name: 'api_study_create_dataset')]
    public function createDataset(
        #[MapEntity(mapping: ['study' => 'id'])]
        Study $study,
    ): Response {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        try {
            $envelope = $this->bus->dispatch(new CreateDatasetForStudyCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $dataset = $handledStamp->getResult();

            return new JsonResponse((new DatasetApiResource($dataset))->toArray());
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while creating a dataset for a study',
                [
                    'exception' => $e,
                    'Study' => $study->getSlug(),
                    'StudyID' => $study->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/study/{study}")
 * @ParamConverter("study", options={"mapping": {"study": "id"}})
 */
class StudyDatasetApiController extends ApiController
{
    /** @Route("/dataset", methods={"GET"}, name="api_study_datasets") */
    public function datasets(Study $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        $envelope = $bus->dispatch(new GetDatasetsByStudyCommand($study));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $results = $handledStamp->getResult();

        return $this->getPaginatedResponse(
            DatasetApiResource::class,
            $results,
            [DatasetVoter::VIEW, DatasetVoter::EDIT, DatasetVoter::MANAGE]
        );
    }

    /** @Route("/dataset", methods={"POST"}, name="api_study_create_dataset") */
    public function createDataset(Study $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        try {
            $envelope = $bus->dispatch(new CreateDatasetForStudyCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $dataset = $handledStamp->getResult();

            return new JsonResponse((new DatasetApiResource($dataset))->toArray());
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a dataset for a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Request\Study\StudyApiRequest;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\CatalogNotFound;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Message\Catalog\GetCatalogBySlugCommand;
use App\Message\Dataset\CreateDatasetForStudyCommand;
use App\Message\Dataset\GetDatasetsByStudyCommand;
use App\Message\Study\CreateStudyCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/study/{study}")
 * @ParamConverter("study", options={"mapping": {"study": "id"}})
 */
class StudyDatasetApiController extends ApiController
{
    /**
     * @Route("/dataset", methods={"GET"}, name="api_study_datasets")
     */
    public function datasets(Study $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        $envelope = $bus->dispatch(new GetDatasetsByStudyCommand($study));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        $results = $handledStamp->getResult();

        return new JsonResponse((new PaginatedApiResource(DatasetApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
    }

    /**
     * @Route("/dataset", methods={"POST"}, name="api_study_create_dataset")
     */
    public function createDataset(Study $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $envelope = $bus->dispatch(new CreateDatasetForStudyCommand($study));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $dataset = $handledStamp->getResult();

            return new JsonResponse((new DatasetApiResource($dataset))->toArray());
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

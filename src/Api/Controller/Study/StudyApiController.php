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
use App\Message\Study\AddStudyCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/study")
 */
class StudyApiController extends ApiController
{
    /**
     * @Route("/slug/{study}", methods={"GET"}, name="api_study_byslug")
     * @ParamConverter("study", options={"mapping": {"study": "slug"}})
     */
    public function studyBySlug(Study $study): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        return new JsonResponse((new StudyMetadataApiResource($study->getLatestMetadata()))->toArray());
    }

    /**
     * @Route("/{study}", methods={"GET"}, name="api_study")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
     */
    public function study(Study $study): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        return new JsonResponse((new StudyApiResource($study, $this->isGranted('ROLE_ADMIN')))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_add_study")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
     */
    public function addStudy(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            /** @var StudyApiRequest $parsed */
            $parsed = $this->parseRequest(StudyApiRequest::class, $request);

            $catalog = null;

            if($parsed->getCatalog() !== null) {
                $envelope = $bus->dispatch(new GetCatalogBySlugCommand($parsed->getCatalog()));

                /** @var HandledStamp $handledStamp */
                $handledStamp = $envelope->last(HandledStamp::class);

                /** @var Catalog $catalog */
                $catalog = $handledStamp->getResult();

                $this->denyAccessUnlessGranted('add', $catalog);
            }

            $envelope = $bus->dispatch(new AddStudyCommand($parsed->getSource(), $parsed->getId(), $parsed->getSourceServer(), $parsed->getName(), true));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Study $study */
            $study = $handledStamp->getResult();

            if($catalog !== null) {
                $bus->dispatch(new AddStudyToCatalogCommand($study, $catalog));
            }

            return new JsonResponse((new StudyApiResource($study))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof CatalogNotFound) {
                return new JsonResponse($e->toArray(), 404);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), 403);
            }

            if ($e instanceof StudyAlreadyExists) {
                return new JsonResponse($e->toArray(), 409);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{study}/dataset", methods={"GET"}, name="api_study_datasets")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
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
     * @Route("/{study}/dataset", methods={"POST"}, name="api_study_create_dataset")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
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

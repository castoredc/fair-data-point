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
use App\Message\Study\UpdateStudyCommand;
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
     * @Route("/{study}", methods={"POST"}, name="api_update_study")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
     */
    public function updateStudy(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        try {
            /** @var StudyApiRequest $parsed */
            $parsed = $this->parseRequest(StudyApiRequest::class, $request);

            $bus->dispatch(new UpdateStudyCommand($study, $parsed->getSourceId(), $parsed->getSourceServer(), $parsed->getName(), $parsed->getSlug(), $parsed->getPublished()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

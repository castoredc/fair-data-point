<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Request\Catalog\AddStudyToCatalogApiRequest;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use App\Message\Castor\ImportStudyCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudyCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/catalog/{catalog}/study")
 * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
 */
class CatalogStudiesApiController extends ApiController
{
    /**
     * @Route("", name="api_catalog_studies")
     */
    public function studies(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            /** @var StudyMetadataFilterApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new GetPaginatedStudiesCommand(
                    $catalog,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    null,
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(StudyApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the studies for a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/filters", name="api_catalog_studies_filters")
     */
    public function studyFilters(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);
        $studies = $catalog->getStudies($this->isGranted('edit', $catalog));

        return new JsonResponse((new StudiesFilterApiResource($studies))->toArray());
    }

    /**
     * @Route("/add", methods={"POST"}, name="api_add_study_to_catalog")
     */
    public function addStudyToCatalog(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);

        try {
            /** @var AddStudyToCatalogApiRequest $parsed */
            $parsed = $this->parseRequest(AddStudyToCatalogApiRequest::class, $request);

            $envelope = $bus->dispatch(new GetStudyCommand($parsed->getStudyId()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Study $study */
            $study = $handledStamp->getResult();

            $this->denyAccessUnlessGranted('edit', $study);

            $bus->dispatch(new AddStudyToCatalogCommand($study, $catalog));

            return new JsonResponse((new StudyApiResource($study))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), 404);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }

            $this->logger->critical('An error occurred while adding a study to a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/import", methods={"POST"}, name="api_import_study_to_catalog")
     */
    public function importStudyToCatalog(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);

        try {
            /** @var AddStudyToCatalogApiRequest $parsed */
            $parsed = $this->parseRequest(AddStudyToCatalogApiRequest::class, $request);

            $envelope = $bus->dispatch(new ImportStudyCommand($parsed->getStudyId()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Study $study */
            $study = $handledStamp->getResult();

            $this->denyAccessUnlessGranted('edit', $study);

            $bus->dispatch(new AddStudyToCatalogCommand($study, $catalog));

            return new JsonResponse((new StudyApiResource($study))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), 404);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }

            $this->logger->critical('An error occurred while importing a study to a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\AddStudyToCatalogApiRequest;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Command\Castor\ImportStudyCommand;
use App\Command\Study\AddStudyToCatalogCommand;
use App\Command\Study\GetPaginatedStudiesCommand;
use App\Command\Study\GetStudyCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use App\Security\Authorization\Voter\StudyVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

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
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedStudiesCommand(
                    $catalog,
                    null,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    null,
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                StudyApiResource::class,
                $results,
                [StudyVoter::VIEW, StudyVoter::EDIT, StudyVoter::EDIT_SOURCE_SYSTEM]
            );
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
            $parsed = $this->parseRequest(AddStudyToCatalogApiRequest::class, $request);
            assert($parsed instanceof AddStudyToCatalogApiRequest);

            $envelope = $bus->dispatch(new GetStudyCommand($parsed->getStudyId()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $study = $handledStamp->getResult();
            assert($study instanceof Study);

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
            $parsed = $this->parseRequest(AddStudyToCatalogApiRequest::class, $request);
            assert($parsed instanceof AddStudyToCatalogApiRequest);

            $envelope = $bus->dispatch(new ImportStudyCommand($parsed->getStudyId()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $study = $handledStamp->getResult();
            assert($study instanceof Study);

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

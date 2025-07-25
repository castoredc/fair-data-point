<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\AddStudyToCatalogApiRequest;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
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
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Authorization\Voter\StudyVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/catalog/{catalog}/study')]
class CatalogStudiesApiController extends ApiController
{
    #[Route(path: '', name: 'api_catalog_studies')]
    public function studies(
        #[MapEntity(mapping: ['catalog' => 'slug'])]
        Catalog $catalog,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $this->bus->dispatch(
                new GetPaginatedStudiesCommand(
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    $this->isGranted('edit', $catalog),
                    $catalog,
                    null,
                    null
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
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the studies for a catalog',
                [
                    'exception' => $e,
                    'Catalog' => $catalog->getSlug(),
                    'CatalogID' => $catalog->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/add', methods: ['POST'], name: 'api_add_study_to_catalog')]
    public function addStudyToCatalog(
        #[MapEntity(mapping: ['catalog' => 'slug'])]
        Catalog $catalog,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(CatalogVoter::ADD, $catalog);

        try {
            $parsed = $this->parseRequest(AddStudyToCatalogApiRequest::class, $request);
            assert($parsed instanceof AddStudyToCatalogApiRequest);

            $envelope = $this->bus->dispatch(new GetStudyCommand($parsed->getStudyId()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $study = $handledStamp->getResult();
            assert($study instanceof Study);

            $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

            $this->bus->dispatch(new AddStudyToCatalogCommand($study, $catalog));

            return new JsonResponse((new StudyApiResource($study))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            $this->logger->critical(
                'An error occurred while adding a study to a catalog',
                [
                    'exception' => $e,
                    'Catalog' => $catalog->getSlug(),
                    'CatalogID' => $catalog->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/import', methods: ['POST'], name: 'api_import_study_to_catalog')]
    public function importStudyToCatalog(
        #[MapEntity(mapping: ['catalog' => 'slug'])]
        Catalog $catalog,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(CatalogVoter::ADD, $catalog);

        try {
            $parsed = $this->parseRequest(AddStudyToCatalogApiRequest::class, $request);
            assert($parsed instanceof AddStudyToCatalogApiRequest);

            $envelope = $this->bus->dispatch(new ImportStudyCommand($parsed->getStudyId()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $study = $handledStamp->getResult();
            assert($study instanceof Study);

            $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

            $this->bus->dispatch(new AddStudyToCatalogCommand($study, $catalog));

            return new JsonResponse((new StudyApiResource($study))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            $this->logger->critical(
                'An error occurred while importing a study to a catalog',
                [
                    'exception' => $e,
                    'Catalog' => $catalog->getSlug(),
                    'CatalogID' => $catalog->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

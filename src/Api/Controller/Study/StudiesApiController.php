<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Request\Study\StudyApiRequest;
use App\Api\Resource\Study\StudyApiResource;
use App\Command\Catalog\GetCatalogBySlugCommand;
use App\Command\Study\AddStudyToCatalogCommand;
use App\Command\Study\CreateStudyCommand;
use App\Command\Study\GetPaginatedStudiesCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\CatalogNotFound;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Authorization\Voter\StudyVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/study')]
class StudiesApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_studies')]
    public function studies(Request $request): Response
    {
        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $this->bus->dispatch(
                new GetPaginatedStudiesCommand(
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    null,
                    null,
                    $parsed->getHideCatalogs()
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
            $this->logger->critical('An error occurred while getting the studies', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '', methods: ['POST'], name: 'api_add_study')]
    public function addStudy(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(StudyApiRequest::class, $request);
            assert($parsed instanceof StudyApiRequest);

            $catalog = null;

            if ($parsed->getCatalog() !== null) {
                $envelope = $this->bus->dispatch(new GetCatalogBySlugCommand($parsed->getCatalog()));

                $handledStamp = $envelope->last(HandledStamp::class);
                assert($handledStamp instanceof HandledStamp);

                $catalog = $handledStamp->getResult();
                assert($catalog instanceof Catalog);

                $this->denyAccessUnlessGranted(CatalogVoter::ADD, $catalog);
            }

            $envelope = $this->bus->dispatch(
                new CreateStudyCommand(
                    $parsed->getSource(),
                    true,
                    $parsed->getSourceId(),
                    $parsed->getSourceServer(),
                    $parsed->getName()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $study = $handledStamp->getResult();
            assert($study instanceof Study);

            if ($catalog !== null) {
                $this->bus->dispatch(new AddStudyToCatalogCommand($study, $catalog));
            }

            return new JsonResponse((new StudyApiResource($study))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof CatalogNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof StudyAlreadyExists) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while adding a study', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

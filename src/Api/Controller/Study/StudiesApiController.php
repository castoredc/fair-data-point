<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Request\Study\StudyApiRequest;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Command\Catalog\GetCatalogBySlugCommand;
use App\Command\Study\AddStudyToCatalogCommand;
use App\Command\Study\CreateStudyCommand;
use App\Command\Study\GetPaginatedStudiesCommand;
use App\Command\Study\GetStudiesCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\CatalogNotFound;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Exception\UserNotACastorUser;
use App\Security\Authorization\Voter\StudyVoter;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/study")
 */
class StudiesApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_studies")
     */
    public function studies(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedStudiesCommand(
                    null,
                    null,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    $parsed->getHideCatalogs(),
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
            $this->logger->critical('An error occurred while getting the studies', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/filters", name="api_studies_filters")
     */
    public function studiesFilters(MessageBusInterface $bus): Response
    {
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $studies = $this->getStudies($user, $bus);

            return new JsonResponse((new StudiesFilterApiResource($studies))->toArray());
        } catch (UserNotACastorUser $e) {
            return new JsonResponse($e->toArray(), 403);
        }
    }

    /**
     * @Route("", methods={"POST"}, name="api_add_study")
     */
    public function addStudy(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(StudyApiRequest::class, $request);
            assert($parsed instanceof StudyApiRequest);

            $catalog = null;

            if ($parsed->getCatalog() !== null) {
                $envelope = $bus->dispatch(new GetCatalogBySlugCommand($parsed->getCatalog()));

                $handledStamp = $envelope->last(HandledStamp::class);
                assert($handledStamp instanceof HandledStamp);

                $catalog = $handledStamp->getResult();
                assert($catalog instanceof Catalog);

                $this->denyAccessUnlessGranted('add', $catalog);
            }

            $envelope = $bus->dispatch(
                new CreateStudyCommand($parsed->getSource(), $parsed->getSourceId(), $parsed->getSourceServer(), $parsed->getName(), true)
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $study = $handledStamp->getResult();
            assert($study instanceof Study);

            if ($catalog !== null) {
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

            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), 403);
            }

            if ($e instanceof StudyAlreadyExists) {
                return new JsonResponse($e->toArray(), 409);
            }

            $this->logger->critical('An error occurred while adding a study', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return Study[]
     *
     * @throws UserNotACastorUser
     */
    private function getStudies(User $user, MessageBusInterface $bus): array
    {
        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $envelope = $bus->dispatch(new GetStudiesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return $handledStamp->getResult();
    }
}

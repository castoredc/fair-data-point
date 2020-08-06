<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Request\Study\StudyApiRequest;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\CatalogNotFound;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Exception\UserNotACastorUser;
use App\Message\Catalog\GetCatalogBySlugCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Message\Study\CreateStudyCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudiesCommand;
use App\Security\User;
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
class StudiesApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_studies")
     */
    public function studies(Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyMetadataFilterApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new GetPaginatedStudiesCommand(
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

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(StudyApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the studies', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/filters", name="api_studies_filters")
     */
    public function studiesFilters(MessageBusInterface $bus): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

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
            /** @var StudyApiRequest $parsed */
            $parsed = $this->parseRequest(StudyApiRequest::class, $request);

            $catalog = null;

            if ($parsed->getCatalog() !== null) {
                $envelope = $bus->dispatch(new GetCatalogBySlugCommand($parsed->getCatalog()));

                /** @var HandledStamp $handledStamp */
                $handledStamp = $envelope->last(HandledStamp::class);

                /** @var Catalog $catalog */
                $catalog = $handledStamp->getResult();

                $this->denyAccessUnlessGranted('add', $catalog);
            }

            $envelope = $bus->dispatch(
                new CreateStudyCommand($parsed->getSource(), $parsed->getSourceId(), $parsed->getSourceServer(), $parsed->getName(), true)
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Study $study */
            $study = $handledStamp->getResult();

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

            return new JsonResponse([], 500);
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

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return $handledStamp->getResult();
    }
}

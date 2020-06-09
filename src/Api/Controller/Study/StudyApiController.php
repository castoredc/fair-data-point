<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Castor\Study;
use App\Exception\ApiRequestParseError;
use App\Message\Study\FindStudiesByUserCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudiesCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api/study")
 */
class StudyApiController extends ApiController
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
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/filters", name="api_studies_filters")
     */
    public function studiesFilters(MessageBusInterface $bus): Response
    {
        $studies = $this->getStudies($this->getUser(), $bus);

        return new JsonResponse((new StudiesFilterApiResource($studies))->toArray());
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
     * @Route("/my", methods={"GET"}, name="api_my_studies")
     */
    public function myStudies(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var CastorUser $user */
        $user = $this->getUser();
        $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, false));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult());
    }

    private function getStudies(?UserInterface $user, MessageBusInterface $bus): array
    {
        assert($user instanceof CastorUser);
        $envelope = $bus->dispatch(new GetStudiesCommand($user));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return $handledStamp->getResult();
    }
}

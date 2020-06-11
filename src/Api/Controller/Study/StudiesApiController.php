<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Study;
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
        /** @var CastorUser|null $user */
        $user = $this->getUser();
        $studies = $this->getStudies($user, $bus);

        return new JsonResponse((new StudiesFilterApiResource($studies))->toArray());
    }

    /** @return Study[] */
    private function getStudies(?UserInterface $user, MessageBusInterface $bus): array
    {
        assert($user instanceof CastorUser);
        $envelope = $bus->dispatch(new GetStudiesCommand($user));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return $handledStamp->getResult();
    }
}

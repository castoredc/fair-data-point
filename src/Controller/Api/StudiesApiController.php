<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\CastorStudyApiRequest;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\AddStudyToCatalogCommand;
use App\Message\Api\Study\FindStudiesByUserCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class StudiesApiController extends ApiController
{
    /**
     * @Route("/api/study", name="api_studies")
     */
    public function studies(MessageBusInterface $bus): Response
    {
        /** @var CastorUser $user */
        $user = $this->getUser();
        $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, false));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult());
    }

    /**
     * @Route("/api/catalog/{catalog}/study/add", methods={"POST"}, name="api_add_study")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addCastorStudy(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var CastorStudyApiRequest $parsed */
            $parsed = $this->parseRequest(CastorStudyApiRequest::class, $request);
            $envelope = $bus->dispatch(new AddCastorStudyCommand($parsed->getStudyId(), $user));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $bus->dispatch(new AddStudyToCatalogCommand($handledStamp->getResult(), $catalog));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyAlreadyExists) {
                return new JsonResponse($e->toArray(), 409);
            }
            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }
        }

        return new JsonResponse([], 500);
    }
}

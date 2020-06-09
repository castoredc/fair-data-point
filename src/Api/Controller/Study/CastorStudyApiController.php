<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Request\Study\CastorStudyApiRequest;
use App\Api\Resource\Study\StudiesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\SessionTimedOut;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\AddCastorStudyCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Message\Study\FindStudiesByUserCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CastorStudyApiController extends ApiController
{
    /**
     * @Route("/api/castor/studies", name="api_castor_studies")
     */
    public function castorStudies(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, true, $request->get('hide') !== null));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse((new StudiesApiResource($handledStamp->getResult(), false))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), 401);
            }
            if ($e instanceof NoAccessPermissionToStudy) {
                return new JsonResponse($e->toArray(), 403);
            }
        }

        return new JsonResponse([], 500);
    }

    /**
     * @Route("/api/catalog/{catalog}/study/add", methods={"POST"}, name="api_add_study")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addCastorStudy(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);

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

            return new JsonResponse([], 500);
        }
    }
}

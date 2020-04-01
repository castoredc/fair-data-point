<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Study\ManualCastorStudyApiRequest;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\AddManualCastorStudyCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class AdminStudiesApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}/study/add/manual", methods={"POST"}, name="api_add_study_manual")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     */
    public function addCastorStudy(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var ManualCastorStudyApiRequest $parsed */
            $parsed = $this->parseRequest(ManualCastorStudyApiRequest::class, $request);
            $envelope = $bus->dispatch(new AddManualCastorStudyCommand($parsed->getStudyId(), $parsed->getStudyName(), $parsed->getStudySlug(), $user));

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
        }

        return new JsonResponse([], 500);
    }
}

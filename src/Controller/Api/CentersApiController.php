<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Study\Provenance\StudyCenterApiRequest;
use App\Entity\Study;
use App\Exception\GroupedApiRequestParseError;
use App\Message\Agent\CreateDepartmentAndOrganizationCommand;
use App\Message\Study\Provenance\ClearStudyCentersCommand;
use App\Message\Study\Provenance\GetStudyCentersCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CentersApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/centers", methods={"GET"}, name="api_get_centers")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function getCenters(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        try {
            $envelope = $bus->dispatch(new GetStudyCentersCommand($study));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult()->toArray());
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/centers/add", methods={"POST"}, name="api_add_centers")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function addCenters(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            /** @var StudyCenterApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(StudyCenterApiRequest::class, $request);

            $bus->dispatch(new ClearStudyCentersCommand($study));

            foreach ($parsed as $item) {
                $bus->dispatch(
                    new CreateDepartmentAndOrganizationCommand(
                        $study,
                        null,
                        null,
                        $item->getName(),
                        null,
                        $item->getCountry(),
                        $item->getCity(),
                        $item->getDepartment(),
                        $item->getAdditionalInformation(),
                        $this->isGranted('ROLE_ADMIN') ? $item->getCoordinatesLatitude() : null,
                        $this->isGranted('ROLE_ADMIN') ? $item->getCoordinatesLongitude() : null,
                    )
                );
            }

            return new JsonResponse([], 200);
        } catch (GroupedApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

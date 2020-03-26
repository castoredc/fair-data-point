<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\StudyCenterApiRequest;
use App\Entity\Castor\Study;
use App\Exception\GroupedApiRequestParseException;
use App\Message\Api\Study\ClearStudyCentersCommand;
use App\Message\Api\Study\CreateDepartmentAndOrganizationCommand;
use App\Message\Api\Study\GetStudyCentersCommand;
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
        try {
            $envelope = $bus->dispatch(new GetStudyCentersCommand($study));

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
        try {
            /** @var StudyCenterApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(StudyCenterApiRequest::class, $request);

            $envelope = $bus->dispatch(new ClearStudyCentersCommand($study));
            $handledStamp = $envelope->last(HandledStamp::class);

            if ($handledStamp) {
                foreach ($parsed as $item) {
                    $envelope = $bus->dispatch(
                        new CreateDepartmentAndOrganizationCommand(
                            $study,
                            null,
                            null,
                            $item->getName(),
                            null,
                            $item->getCountry(),
                            $item->getCity(),
                            $item->getDepartment(),
                            $item->getAdditionalInformation()
                        )
                    );

                    $handledStamp = $envelope->last(HandledStamp::class);
                }

                return new JsonResponse([], 200);
            }
        } catch (GroupedApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Study\Provenance\StudyCenterApiRequest;
use App\Api\Resource\Metadata\ParticipatingCentersApiResource;
use App\Command\Agent\AddStudyCenterCommand;
use App\Command\Agent\CreateStudyCenterCommand;
use App\Command\Agent\RemoveStudyCenterCommand;
use App\Command\Study\Provenance\GetStudyCentersCommand;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class StudyCentersApiController extends ApiController
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

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new ParticipatingCentersApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the centers for a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $parsed = $this->parseRequest(StudyCenterApiRequest::class, $request);
            assert($parsed instanceof StudyCenterApiRequest);

            if ($parsed->getSource()->isDatabase()) {
                $bus->dispatch(
                    new AddStudyCenterCommand(
                        $study,
                        $parsed->getId()
                    )
                );
            } elseif ($parsed->getSource()->isManual()) {
                $bus->dispatch(
                    new CreateStudyCenterCommand(
                        $study,
                        $parsed->getName(),
                        $parsed->getCountry(),
                        $parsed->getCity(),
                    )
                );
            }

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a center to a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/study/{studyId}/centers/remove", methods={"POST"}, name="api_remove_centers")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function removeCenters(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $parsed = $this->parseRequest(StudyCenterApiRequest::class, $request);
            assert($parsed instanceof StudyCenterApiRequest);

            $bus->dispatch(
                new RemoveStudyCenterCommand(
                    $study,
                    $parsed->getId()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while removing a center from a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

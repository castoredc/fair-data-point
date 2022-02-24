<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Study\Provenance\StudyContactApiRequest;
use App\Api\Resource\Agent\Person\PersonsApiResource;
use App\Command\Agent\AddStudyContactCommand;
use App\Command\Agent\RemoveStudyContactCommand;
use App\Command\Study\Provenance\GetStudyContactsCommand;
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

class StudyTeamApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/team", methods={"GET"}, name="api_get_contacts")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function getTeam(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        try {
            $envelope = $bus->dispatch(new GetStudyContactsCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new PersonsApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the study team for a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/study/{studyId}/team/add", methods={"POST"}, name="api_add_contacts")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function addTeamMember(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $parsed = $this->parseRequest(StudyContactApiRequest::class, $request);
            assert($parsed instanceof StudyContactApiRequest);

            $bus->dispatch(
                new AddStudyContactCommand(
                    $study,
                    $parsed->getId(),
                    $parsed->getFirstName(),
                    $parsed->getMiddleName(),
                    $parsed->getLastName(),
                    $parsed->getEmail(),
                    $parsed->getOrcid()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            $this->logger->critical('An error occurred while adding a study team member to a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/study/{studyId}/team/remove", methods={"POST"}, name="api_remove_contact")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function removeTeamMember(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $parsed = $this->parseRequest(StudyContactApiRequest::class, $request);
            assert($parsed instanceof StudyContactApiRequest);

            $bus->dispatch(
                new RemoveStudyContactCommand(
                    $study,
                    $parsed->getId()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            $this->logger->critical('An error occurred while removing a study team member from a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        }
    }
}

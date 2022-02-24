<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Study\StudyApiRequest;
use App\Api\Resource\Study\StudyApiResource;
use App\Command\Study\UpdateStudyCommand;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\StudyVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/study")
 */
class StudyApiController extends ApiController
{
    /**
     * @Route("/slug/{study}", methods={"GET"}, name="api_study_byslug")
     * @ParamConverter("study", options={"mapping": {"study": "slug"}})
     */
    public function studyBySlug(Study $study): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        return $this->getResponse(
            new StudyApiResource($study),
            $study,
            [StudyVoter::VIEW, StudyVoter::EDIT, StudyVoter::EDIT_SOURCE_SYSTEM]
        );
    }

    /**
     * @Route("/{study}", methods={"GET"}, name="api_study")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
     */
    public function study(Study $study): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        return $this->getResponse(
            new StudyApiResource($study, $this->isGranted('ROLE_ADMIN')),
            $study,
            [StudyVoter::VIEW, StudyVoter::EDIT, StudyVoter::EDIT_SOURCE_SYSTEM]
        );
    }

    /**
     * @Route("/{study}", methods={"POST"}, name="api_update_study")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
     */
    public function updateStudy(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        try {
            $parsed = $this->parseRequest(StudyApiRequest::class, $request);
            assert($parsed instanceof StudyApiRequest);

            $bus->dispatch(new UpdateStudyCommand($study, $parsed->getSourceId(), $parsed->getSourceServer(), $parsed->getName(), $parsed->getSlug(), $parsed->getPublished()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a study', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

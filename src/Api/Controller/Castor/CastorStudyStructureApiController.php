<?php
declare(strict_types=1);

namespace App\Api\Controller\Castor;

use App\Api\Controller\ApiController;
use App\Api\Resource\Study\InstitutesApiResource;
use App\Api\Resource\StudyStructure\FieldsApiResource;
use App\Api\Resource\StudyStructure\OptionGroupsApiResource;
use App\Api\Resource\StudyStructure\StudyStructureApiResource;
use App\Command\Study\GetFieldsForStepCommand;
use App\Command\Study\GetInstitutesForStudyCommand;
use App\Command\Study\GetOptionGroupsForStudyCommand;
use App\Command\Study\GetStudyStructureCommand;
use App\Entity\Castor\CastorStudy;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Security\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/castor/study/{study}")
 * @ParamConverter("study", options={"mapping": {"study": "id"}})
 */
class CastorStudyStructureApiController extends ApiController
{
    /** @Route("/structure", name="api_study_structure") */
    public function studyStructure(CastorStudy $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $envelope = $bus->dispatch(new GetStudyStructureCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new StudyStructureApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof ErrorFetchingCastorData) {
                return new JsonResponse($e->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNAUTHORIZED);
            }

            $this->logger->critical(
                'An error occurred while getting the study structure',
                [
                    'exception' => $e,
                    'Study' => $study->getSlug(),
                    'StudyID' => $study->getId(),
                ]
            );
        }

        return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @Route("/structure/step/{step}/fields", name="api_study_structure_step") */
    public function studyStructureStep(CastorStudy $study, string $step, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $envelope = $bus->dispatch(new GetFieldsForStepCommand($study, $step));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new FieldsApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof ErrorFetchingCastorData) {
                return new JsonResponse($e->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNAUTHORIZED);
            }

            $this->logger->critical(
                'An error occurred while getting the study structure step',
                [
                    'exception' => $e,
                    'Study' => $study->getSlug(),
                    'StudyID' => $study->getId(),
                    'Step' => $step,
                ]
            );
        }

        return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @Route("/optiongroups", name="api_study_optiongroups") */
    public function optionGroups(CastorStudy $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $envelope = $bus->dispatch(new GetOptionGroupsForStudyCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new OptionGroupsApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof ErrorFetchingCastorData) {
                return new JsonResponse($e->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNAUTHORIZED);
            }

            $this->logger->critical(
                'An error occurred while getting the option groups',
                [
                    'exception' => $e,
                    'Study' => $study->getSlug(),
                    'StudyID' => $study->getId(),
                ]
            );
        }

        return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @Route("/institutes", name="api_study_institutes") */
    public function institutes(CastorStudy $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $envelope = $bus->dispatch(new GetInstitutesForStudyCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new InstitutesApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof ErrorFetchingCastorData) {
                return new JsonResponse($e->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNAUTHORIZED);
            }

            $this->logger->critical(
                'An error occurred while getting the institutes',
                [
                    'exception' => $e,
                    'Study' => $study->getSlug(),
                    'StudyID' => $study->getId(),
                ]
            );
        }

        return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

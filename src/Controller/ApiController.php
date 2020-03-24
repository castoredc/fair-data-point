<?php

namespace App\Controller;

use App\Exception\ApiRequestParseException;
use App\Exception\NoPermissionException;
use App\Exception\SessionTimeOutException;
use App\Exception\StudyAlreadyExistsException;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\CreateStudyMetadataCommand;
use App\Message\Api\Study\FindStudiesByUserCommand;
use App\Model\Castor\ApiClient;
use App\Request\ApiRequest;
use App\Request\CastorStudyApiRequest;
use App\Request\StudyMetadataApiRequest;
use App\Security\CastorUser;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    /** @var ApiClient */
    private $apiClient;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ApiClient $apiClient, ValidatorInterface $validator)
    {
        $this->apiClient = $apiClient;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/user", name="api_user")
     */
    public function user(): Response
    {
        /** @var CastorUser|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return new JsonResponse([], 401);
        }

        return new JsonResponse($user);
    }

    /**
     * @Route("/api/studies", name="api_studies")
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function studies(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new FindStudiesByUserCommand($this->getUser(), false));
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult());
    }

    /**
     * @Route("/api/studies/add", methods={"POST"}, name="api_add_study")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function addCastorStudy(Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var CastorStudyApiRequest $parsed */
            $parsed = $this->parseRequest(CastorStudyApiRequest::class, $request);

            $envelope = $bus->dispatch(new AddCastorStudyCommand($parsed->getStudyId(), $this->getUser()));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch(HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyAlreadyExistsException) {
                return new JsonResponse($e->toArray(), 409);
            }
        }
    }

    /**
     * @Route("/api/studies/metadata/add", methods={"POST"}, name="api_add_metadata")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function addMetadata(Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyMetadataApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataApiRequest::class, $request);

            $envelope = $bus->dispatch(new CreateStudyMetadataCommand(
                $parsed->getStudyId(),
                $parsed->getBriefName(),
                $parsed->getScientificName(),
                $parsed->getBriefSummary(),
                $parsed->getSummary(),
                $parsed->getType(),
                $parsed->getCondition(),
                $parsed->getIntervention(),
                $parsed->getEstimatedEnrollment(),
                $parsed->getEstimatedStudyStartDate(),
                $parsed->getEstimatedStudyCompletionDate(),
                $this->getUser()
            ));

            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch(HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/castor/studies", name="api_castor_studies")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function castorStudies(Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new FindStudiesByUserCommand($this->getUser(), true, $request->get('hide')));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult());
        } catch(HandlerFailedException $e) {
            $e = $e->getPrevious();

            if($e instanceof SessionTimeOutException)
            {
                return new JsonResponse($e->toArray(), 401);
            }
            else if($e instanceof NoPermissionException)
            {
                return new JsonResponse($e->toArray(), 403);
            }
        }
    }


    /**
     * @throws ApiRequestParseException
     */
    protected function parseRequest(string $requestObject, Request $request): ApiRequest
    {
        $request = new $requestObject($request);

        $errors = $this->validator->validate($request);

        if ($errors->count() > 0) {
            throw new ApiRequestParseException($errors);
        }

        return $request;
    }
}
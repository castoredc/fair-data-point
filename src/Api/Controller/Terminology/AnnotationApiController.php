<?php
declare(strict_types=1);

namespace App\Api\Controller\Terminology;

use App\Api\Request\Terminology\AnnotationApiRequest;
use App\Controller\Api\ApiController;
use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\AnnotationAlreadyExists;
use App\Exception\ApiRequestParseError;
use App\Exception\InvalidEntityType;
use App\Exception\OntologyConceptNotFound;
use App\Exception\OntologyNotFound;
use App\Message\Castor\GetCastorEntityCommand;
use App\Message\Terminology\AddAnnotationCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/study/{study}/annotations")
 * @ParamConverter("study", options={"mapping": {"study": "id"}})
 */
class AnnotationApiController extends ApiController
{
    /**
     * @Route("/add", name="api_study_annotations_add")
     * @ParamConverter("study", options={"mapping": {"study": "id"}})
     */
    public function addAnnotation(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        /** @var CastorUser|null $user */
        $user = $this->getUser();

        try {
            /** @var AnnotationApiRequest $parsed */
            $parsed = $this->parseRequest(AnnotationApiRequest::class, $request);

            assert($study instanceof CastorStudy);

            $envelope = $bus->dispatch(new GetCastorEntityCommand($study, $user, $parsed->getEntityType(), $parsed->getEntityId(), $parsed->getEntityParent()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var CastorEntity $entity */
            $entity = $handledStamp->getResult();

            $bus->dispatch(new AddAnnotationCommand($study, $entity, $parsed->getOntology(), $parsed->getConcept()));

            return new JsonResponse([], Response::HTTP_OK);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof OntologyNotFound || $e instanceof OntologyConceptNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }
            if ($e instanceof InvalidEntityType) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }
            if ($e instanceof AnnotationAlreadyExists) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while adding an annotation', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

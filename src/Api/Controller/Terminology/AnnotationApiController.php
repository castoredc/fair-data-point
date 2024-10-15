<?php
declare(strict_types=1);

namespace App\Api\Controller\Terminology;

use App\Api\Controller\ApiController;
use App\Api\Request\Terminology\AnnotationApiRequest;
use App\Command\Castor\GetCastorEntityCommand;
use App\Command\Terminology\AddAnnotationCommand;
use App\Command\Terminology\DeleteAnnotationCommand;
use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Entity\Terminology\Annotation;
use App\Exception\AnnotationAlreadyExists;
use App\Exception\ApiRequestParseError;
use App\Exception\InvalidEntityType;
use App\Exception\OntologyConceptNotFound;
use App\Exception\OntologyNotFound;
use App\Security\Authorization\Voter\StudyVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/study/{study}/annotations')]
#[ParamConverter('study', options: ['mapping' => ['study' => 'id']])]
class AnnotationApiController extends ApiController
{
    #[Route(path: '/add', name: 'api_study_annotations_add')]
    public function addAnnotation(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        try {
            $parsed = $this->parseRequest(AnnotationApiRequest::class, $request);
            assert($parsed instanceof AnnotationApiRequest);

            assert($study instanceof CastorStudy);

            $envelope = $bus->dispatch(new GetCastorEntityCommand($study, $parsed->getEntityType(), $parsed->getEntityId(), $parsed->getEntityParent()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $entity = $handledStamp->getResult();
            assert($entity instanceof CastorEntity);

            $bus->dispatch(new AddAnnotationCommand($study, $entity, $parsed->getOntology(), $parsed->getConcept(), $parsed->getConceptType()));

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

    #[Route(path: '/{annotation}', methods: ['DELETE'], name: 'api_study_annotations_delete')]
    public function deleteAnnotation(#[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['annotation' => 'id'])]
    Annotation $annotation, Study $study, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(StudyVoter::EDIT, $study);

        if ($annotation->getEntity()->getStudy() !== $study) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteAnnotationCommand($annotation));

            return new JsonResponse([], Response::HTTP_OK);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while removing an annotation', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

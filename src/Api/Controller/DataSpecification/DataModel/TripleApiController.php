<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\Common\Model\TripleApiRequest;
use App\Api\Resource\DataSpecification\DataModel\TriplesApiResource;
use App\Command\DataSpecification\DataModel\CreateTripleCommand;
use App\Command\DataSpecification\DataModel\DeleteTripleCommand;
use App\Command\DataSpecification\DataModel\UpdateTripleCommand;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/data-model/{model}/v/{version}/module/{module}/triple')]
class TripleApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_data_model_module')]
    public function getTriples(
        #[MapEntity(mapping: ['module' => 'id', 'version' => 'version'])]
        DataModelGroup $module,
    ): Response {
        $this->denyAccessUnlessGranted('view', $module->getVersion()->getDataSpecification());

        return new JsonResponse((new TriplesApiResource($module))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_data_model_triple_add')]
    public function addTriple(
        #[MapEntity(mapping: ['module' => 'id', 'version' => 'version'])]
        DataModelGroup $module,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

        try {
            $parsed = $this->parseRequest(TripleApiRequest::class, $request);
            assert($parsed instanceof TripleApiRequest);

            $this->bus->dispatch(
                new CreateTripleCommand(
                    $module,
                    $parsed->getObjectType(),
                    $parsed->getSubjectType(),
                    $parsed->getObjectValue(),
                    $parsed->getPredicateValue(),
                    $parsed->getSubjectValue()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a data model triple', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{triple}', methods: ['POST'], name: 'api_data_model_triple_update')]
    public function updateTriple(
        #[MapEntity(mapping: ['module' => 'id', 'version' => 'version'])]
        DataModelGroup $module,
        #[MapEntity(mapping: ['triple' => 'id'])]
        Triple $triple,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

        if ($triple->getDataModelVersion() !== $module->getVersion()) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(TripleApiRequest::class, $request);
            assert($parsed instanceof TripleApiRequest);

            $this->bus->dispatch(
                new UpdateTripleCommand(
                    $triple,
                    $parsed->getObjectType(),
                    $parsed->getSubjectType(),
                    $parsed->getObjectValue(),
                    $parsed->getPredicateValue(),
                    $parsed->getSubjectValue()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating a data model triple',
                [
                    'exception' => $e,
                    'TripleID' => $triple->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{triple}', methods: ['DELETE'], name: 'api_data_model_triple_delete')]
    public function deleteTriple(
        #[MapEntity(mapping: ['module' => 'id', 'version' => 'version'])]
        DataModelGroup $module,
        #[MapEntity(mapping: ['triple' => 'id'])]
        Triple $triple,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

        if ($triple->getDataModelVersion() !== $module->getVersion()) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->bus->dispatch(new DeleteTripleCommand($triple));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting a data model triple',
                [
                    'exception' => $e,
                    'TripleID' => $triple->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

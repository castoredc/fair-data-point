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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/data-model/{model}/v/{version}/module/{module}/triple')]
#[ParamConverter('module', options: ['mapping' => ['module' => 'id', 'version' => 'version']])]
class TripleApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_data_model_module')]
    public function getTriples(DataModelGroup $module): Response
    {
        $this->denyAccessUnlessGranted('view', $module->getVersion()->getDataSpecification());

        return new JsonResponse((new TriplesApiResource($module))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_data_model_triple_add')]
    public function addTriple(DataModelGroup $module, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

        try {
            $parsed = $this->parseRequest(TripleApiRequest::class, $request);
            assert($parsed instanceof TripleApiRequest);

            $bus->dispatch(new CreateTripleCommand($module, $parsed->getObjectType(), $parsed->getObjectValue(), $parsed->getPredicateValue(), $parsed->getSubjectType(), $parsed->getSubjectValue()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a data model triple', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{triple}', methods: ['POST'], name: 'api_data_model_triple_update')]
    public function updateTriple(DataModelGroup $module, #[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['triple' => 'id'])]
    Triple $triple, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

        if ($triple->getDataModelVersion() !== $module->getVersion()) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(TripleApiRequest::class, $request);
            assert($parsed instanceof TripleApiRequest);

            $bus->dispatch(new UpdateTripleCommand($triple, $parsed->getObjectType(), $parsed->getObjectValue(), $parsed->getPredicateValue(), $parsed->getSubjectType(), $parsed->getSubjectValue()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model triple', [
                'exception' => $e,
                'TripleID' => $triple->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{triple}', methods: ['DELETE'], name: 'api_data_model_triple_delete')]
    public function deleteTriple(DataModelGroup $module, #[\Symfony\Bridge\Doctrine\Attribute\MapEntity(mapping: ['triple' => 'id'])]
    Triple $triple, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

        if ($triple->getDataModelVersion() !== $module->getVersion()) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteTripleCommand($triple));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a data model triple', [
                'exception' => $e,
                'TripleID' => $triple->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

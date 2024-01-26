<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\Common\Model\TripleApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\TriplesApiResource;
use App\Command\DataSpecification\MetadataModel\CreateTripleCommand;
use App\Command\DataSpecification\MetadataModel\DeleteTripleCommand;
use App\Command\DataSpecification\MetadataModel\UpdateTripleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Exception\ApiRequestParseError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata-model/{model}/v/{version}/module/{module}/triple")
 * @ParamConverter("module", options={"mapping": {"module": "id", "version": "version"}})
 */
class TripleApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_metadata_model_module") */
    public function getTriples(MetadataModelGroup $module): Response
    {
        $this->denyAccessUnlessGranted('view', $module->getVersion()->getDataSpecification());

        return new JsonResponse((new TriplesApiResource($module))->toArray());
    }

    /** @Route("", methods={"POST"}, name="api_metadata_model_triple_add") */
    public function addTriple(MetadataModelGroup $module, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

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

    /**
     * @Route("/{triple}", methods={"POST"}, name="api_metadata_model_triple_update")
     * @ParamConverter("triple", options={"mapping": {"triple": "id"}})
     */
    public function updateTriple(MetadataModelGroup $module, Triple $triple, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

        if ($triple->getMetadataModelVersion() !== $module->getVersion()) {
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

    /**
     * @Route("/{triple}", methods={"DELETE"}, name="api_metadata_model_triple_delete")
     * @ParamConverter("triple", options={"mapping": {"triple": "id"}})
     */
    public function deleteTriple(MetadataModelGroup $module, Triple $triple, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

        if ($triple->getMetadataModelVersion() !== $module->getVersion()) {
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

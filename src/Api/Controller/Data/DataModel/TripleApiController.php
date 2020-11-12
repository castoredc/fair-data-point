<?php
declare(strict_types=1);

namespace App\Api\Controller\Data\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataModel\TripleApiRequest;
use App\Api\Resource\Data\DataModel\TriplesApiResource;
use App\Command\Data\DataModel\CreateTripleCommand;
use App\Command\Data\DataModel\DeleteTripleCommand;
use App\Command\Data\DataModel\UpdateTripleCommand;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Triple;
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
 * @Route("/api/model/{model}/v/{version}/module/{module}/triple")
 * @ParamConverter("module", options={"mapping": {"module": "id", "dataModel": "version"}})
 */
class TripleApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_module")
     */
    public function getTriples(DataModelModule $module): Response
    {
        $this->denyAccessUnlessGranted('view', $module->getDataModel()->getDataModel());

        return new JsonResponse((new TriplesApiResource($module))->toArray(), 200);
    }

    /**
     * @Route("", methods={"POST"}, name="api_triple_add")
     */
    public function addTriple(DataModelModule $module, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getDataModel()->getDataModel());

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
     * @Route("/{triple}", methods={"POST"}, name="api_triple_update")
     * @ParamConverter("triple", options={"mapping": {"triple": "id"}})
     */
    public function updateTriple(DataModelModule $module, Triple $triple, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getDataModel()->getDataModel());

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
     * @Route("/{triple}", methods={"DELETE"}, name="api_triple_delete")
     * @ParamConverter("triple", options={"mapping": {"triple": "id"}})
     */
    public function deleteTriple(DataModelModule $module, Triple $triple, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getDataModel()->getDataModel());

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

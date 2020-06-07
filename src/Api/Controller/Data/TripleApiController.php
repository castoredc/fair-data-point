<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\TripleApiRequest;
use App\Api\Resource\Data\TriplesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModelModule;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateTripleCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/model/{model}/module/{module}/triple")
 * @ParamConverter("module", options={"mapping": {"module": "id", "dataModel": "model"}})
 */
class TripleApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_module")
     */
    public function getTriples(DataModelModule $module): Response
    {
        $this->denyAccessUnlessGranted('view', $module->getDataModel());

        return new JsonResponse((new TriplesApiResource($module))->toArray(), 200);
    }

    /**
     * @Route("/add", name="api_triple_add")
     */
    public function addNode(DataModelModule $module, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getDataModel());

        try {
            /** @var TripleApiRequest $parsed */
            $parsed = $this->parseRequest(TripleApiRequest::class, $request);

            $bus->dispatch(new CreateTripleCommand($module, $parsed->getObjectType(), $parsed->getObjectValue(), $parsed->getPredicateValue(), $parsed->getSubjectType(), $parsed->getSubjectValue()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

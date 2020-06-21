<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\NodeApiRequest;
use App\Api\Resource\Data\NodesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Enum\NodeType;
use App\Exception\ApiRequestParseError;
use App\Exception\InvalidNodeType;
use App\Message\Data\CreateNodeCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/model/{model}/node")
 * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
 */
class NodeApiController extends ApiController
{
    /**
     * @Route("", name="api_node")
     */
    public function nodes(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return new JsonResponse((new NodesApiResource($dataModel))->toArray());
    }

    /**
     * @Route("/{type}", name="api_node_type")
     */
    public function nodesByType(DataModel $dataModel, string $type): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        $nodeType = NodeType::fromString($type);

        return new JsonResponse((new NodesApiResource($dataModel, $nodeType))->toArray());
    }

    /**
     * @Route("/{type}/add", name="api_node_add")
     */
    public function addNode(DataModel $dataModel, string $type, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        $nodeType = NodeType::fromString($type);

        try {
            /** @var NodeApiRequest $parsed */
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);

            $bus->dispatch(new CreateNodeCommand($dataModel, $nodeType, $parsed->getTitle(), $parsed->getDescription(), $parsed->getValue(), $parsed->getDataType()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof InvalidNodeType) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

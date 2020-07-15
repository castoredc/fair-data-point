<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\NodeApiRequest;
use App\Api\Resource\Data\NodesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModelVersion;
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
 * @Route("/api/model/{model}/v/{version}/node")
 * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
 */
class NodeApiController extends ApiController
{
    /**
     * @Route("", name="api_node")
     */
    public function nodes(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new NodesApiResource($dataModelVersion))->toArray());
    }

    /**
     * @Route("/{type}", name="api_node_type")
     */
    public function nodesByType(DataModelVersion $dataModelVersion, string $type): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        $nodeType = NodeType::fromString($type);

        return new JsonResponse((new NodesApiResource($dataModelVersion, $nodeType))->toArray());
    }

    /**
     * @Route("/{type}/add", name="api_node_add")
     */
    public function addNode(DataModelVersion $dataModelVersion, string $type, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        $nodeType = NodeType::fromString($type);

        try {
            /** @var NodeApiRequest $parsed */
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);

            $bus->dispatch(new CreateNodeCommand($dataModelVersion, $nodeType, $parsed->getTitle(), $parsed->getDescription(), $parsed->getValue(), $parsed->getDataType()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof InvalidNodeType) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical('An error occurred while adding a data model node', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

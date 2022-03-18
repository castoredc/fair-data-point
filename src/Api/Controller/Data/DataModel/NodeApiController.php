<?php
declare(strict_types=1);

namespace App\Api\Controller\Data\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataModel\NodeApiRequest;
use App\Api\Resource\Data\DataModel\NodesApiResource;
use App\Command\Data\DataModel\CreateNodeCommand;
use App\Command\Data\DataModel\EditNodeCommand;
use App\Command\Data\DataModel\RemoveNodeCommand;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Enum\NodeType;
use App\Exception\ApiRequestParseError;
use App\Exception\InvalidNodeType;
use App\Exception\NodeInUseByTriples;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/model/{model}/v/{version}/node")
 * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
 */
class NodeApiController extends ApiController
{
    /** @Route("", name="api_node") */
    public function nodes(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new NodesApiResource($dataModelVersion))->toArray());
    }

    /** @Route("/{type}", methods={"GET"}, name="api_node_type") */
    public function nodesByType(DataModelVersion $dataModelVersion, string $type): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        $nodeType = NodeType::fromString($type);

        return new JsonResponse((new NodesApiResource($dataModelVersion, $nodeType))->toArray());
    }

    /** @Route("/{type}", methods={"POST"}, name="api_node_add") */
    public function addNode(DataModelVersion $dataModelVersion, string $type, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        $nodeType = NodeType::fromString($type);

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $bus->dispatch(new CreateNodeCommand($dataModelVersion, $nodeType, $parsed->getTitle(), $parsed->getDescription(), $parsed->getValue(), $parsed->getDataType(), $parsed->isRepeated()));

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

    /**
     * @Route("/{type}/{id}", methods={"POST"}, name="api_node_edit")
     * @ParamConverter("node", options={"mapping": {"id": "id", "version": "version"}})
     */
    public function editNode(DataModelVersion $dataModelVersion, string $type, Node $node, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        if ($node->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $bus->dispatch(new EditNodeCommand($node, $parsed->getTitle(), $parsed->getDescription(), $parsed->getValue(), $parsed->getDataType(), $parsed->isRepeated()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while editing a data model node', [
                'exception' => $e,
                'Node' => $node->getTitle(),
                'NodeId' => $node->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{type}/{id}", methods={"DELETE"}, name="api_node_remove")
     * @ParamConverter("node", options={"mapping": {"id": "id", "version": "version"}})
     */
    public function removeNode(DataModelVersion $dataModelVersion, string $type, Node $node, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        if ($node->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new RemoveNodeCommand($node));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NodeInUseByTriples) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical('An error occurred while editing a data model node', [
                'exception' => $e,
                'Node' => $node->getTitle(),
                'NodeId' => $node->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\MetadataModel\NodeApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\NodesApiResource;
use App\Command\DataSpecification\MetadataModel\CreateNodeCommand;
use App\Command\DataSpecification\MetadataModel\EditNodeCommand;
use App\Command\DataSpecification\MetadataModel\RemoveNodeCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\NodeType;
use App\Exception\ApiRequestParseError;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\DataSpecification\Common\Model\NodeInUseByTriples;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata-model/{model}/v/{version}/node")
 * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
 */
class NodeApiController extends ApiController
{
    /** @Route("", name="api_metadata_model_node") */
    public function nodes(MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new NodesApiResource($metadataModelVersion))->toArray());
    }

    /** @Route("/{type}", methods={"GET"}, name="api_metadata_model_node_type") */
    public function nodesByType(MetadataModelVersion $metadataModelVersion, string $type): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        $nodeType = NodeType::fromString($type);

        return new JsonResponse((new NodesApiResource($metadataModelVersion, $nodeType))->toArray());
    }

    /** @Route("/{type}", methods={"POST"}, name="api_metadata_model_node_add") */
    public function addNode(MetadataModelVersion $metadataModelVersion, string $type, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        $nodeType = NodeType::fromString($type);

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $bus->dispatch(new CreateNodeCommand($metadataModelVersion, $nodeType, $parsed->getTitle(), $parsed->getDescription(), $parsed->getValue(), $parsed->getDataType()));

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
     * @Route("/{type}/{id}", methods={"POST"}, name="api_metadata_model_node_edit")
     * @ParamConverter("node", options={"mapping": {"id": "id", "version": "version"}})
     */
    public function editNode(MetadataModelVersion $metadataModelVersion, string $type, Node $node, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        if ($node->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $bus->dispatch(new EditNodeCommand($node, $parsed->getTitle(), $parsed->getDescription(), $parsed->getValue(), $parsed->getDataType()));

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
     * @Route("/{type}/{id}", methods={"DELETE"}, name="api_metadata_model_node_remove")
     * @ParamConverter("node", options={"mapping": {"id": "id", "version": "version"}})
     */
    public function removeNode(MetadataModelVersion $metadataModelVersion, string $type, Node $node, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        if ($node->getMetadataModelVersion() !== $metadataModelVersion) {
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

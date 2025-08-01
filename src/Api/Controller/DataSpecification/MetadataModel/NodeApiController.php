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
use App\Exception\DataSpecification\MetadataModel\NodeHasValues;
use App\Exception\DataSpecification\MetadataModel\NodeInUseByDisplaySetting;
use App\Exception\DataSpecification\MetadataModel\NodeInUseByField;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata-model/{model}/v/{version}/node')]
class NodeApiController extends ApiController
{
    #[Route(path: '', name: 'api_metadata_model_node')]
    public function nodes(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new NodesApiResource($metadataModelVersion))->toArray());
    }

    #[Route(path: '/{type}', methods: ['GET'], name: 'api_metadata_model_node_type')]
    public function nodesByType(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        string $type,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        $nodeType = NodeType::fromString($type);

        return new JsonResponse((new NodesApiResource($metadataModelVersion, $nodeType))->toArray());
    }

    #[Route(path: '/{type}', methods: ['POST'], name: 'api_metadata_model_node_add')]
    public function addNode(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        string $type,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        $nodeType = NodeType::fromString($type);

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $this->bus->dispatch(
                new CreateNodeCommand(
                    $metadataModelVersion,
                    $nodeType,
                    $parsed->getTitle(),
                    $parsed->getValue(),
                    $parsed->getDescription(),
                    $parsed->getDataType(),
                    $parsed->isRepeated()
                )
            );

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

    #[Route(path: '/{type}/{id}', methods: ['POST'], name: 'api_metadata_model_node_edit')]
    public function editNode(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        string $type,
        #[MapEntity(mapping: ['id' => 'id', 'version' => 'version'])]
        Node $node,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        if ($node->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $this->bus->dispatch(
                new EditNodeCommand(
                    $node,
                    $parsed->getTitle(),
                    $parsed->getValue(),
                    $parsed->getDescription(),
                    $parsed->getDataType(),
                    $parsed->isRepeated()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical(
                'An error occurred while editing a data model node',
                [
                    'exception' => $e,
                    'Node' => $node->getTitle(),
                    'NodeId' => $node->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{type}/{id}', methods: ['DELETE'], name: 'api_metadata_model_node_remove')]
    public function removeNode(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        string $type,
        #[MapEntity(mapping: ['id' => 'id', 'version' => 'version'])]
        Node $node,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        if ($node->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->bus->dispatch(new RemoveNodeCommand($node));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NodeInUseByTriples || $e instanceof NodeInUseByField || $e instanceof NodeInUseByDisplaySetting || $e instanceof NodeHasValues) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical(
                'An error occurred while editing a data model node',
                [
                    'exception' => $e,
                    'Node' => $node->getTitle(),
                    'NodeId' => $node->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

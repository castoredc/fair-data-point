<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\DataModel\NodeApiRequest;
use App\Api\Resource\DataSpecification\DataModel\NodesApiResource;
use App\Command\DataSpecification\DataModel\CreateNodeCommand;
use App\Command\DataSpecification\DataModel\EditNodeCommand;
use App\Command\DataSpecification\DataModel\RemoveNodeCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\NodeType;
use App\Exception\ApiRequestParseError;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\DataSpecification\Common\Model\NodeInUseByTriples;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/data-model/{model}/v/{version}/node')]
class NodeApiController extends ApiController
{
    #[Route(path: '', name: 'api_data_model_node')]
    public function nodes(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new NodesApiResource($dataModelVersion))->toArray());
    }

    #[Route(path: '/{type}', methods: ['GET'], name: 'api_data_model_node_type')]
    public function nodesByType(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        string $type,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        $nodeType = NodeType::fromString($type);

        return new JsonResponse((new NodesApiResource($dataModelVersion, $nodeType))->toArray());
    }

    #[Route(path: '/{type}', methods: ['POST'], name: 'api_data_model_node_add')]
    public function addNode(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        string $type,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

        $nodeType = NodeType::fromString($type);

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $bus->dispatch(
                new CreateNodeCommand(
                    $dataModelVersion,
                    $nodeType,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getValue(),
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

    #[Route(path: '/{type}/{id}', methods: ['POST'], name: 'api_data_model_node_edit')]
    public function editNode(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        string $type,
        #[MapEntity(mapping: ['id' => 'id', 'version' => 'version'])]
        Node $node,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

        if ($node->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(NodeApiRequest::class, $request);
            assert($parsed instanceof NodeApiRequest);

            $bus->dispatch(
                new EditNodeCommand(
                    $node,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getValue(),
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

    #[Route(path: '/{type}/{id}', methods: ['DELETE'], name: 'api_data_model_node_remove')]
    public function removeNode(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        string $type,
        #[MapEntity(mapping: ['id' => 'id', 'version' => 'version'])]
        Node $node,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

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

<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\Common\DataSpecificationPrefixApiRequest;
use App\Api\Resource\DataSpecification\DataModel\DataModelPrefixesApiResource;
use App\Command\DataSpecification\DataModel\CreateDataModelPrefixCommand;
use App\Command\DataSpecification\DataModel\DeleteDataModelPrefixCommand;
use App\Command\DataSpecification\DataModel\UpdateDataModelPrefixCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/data-model/{model}/v/{version}/prefix')]
class DataModelPrefixApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_data_model_prefixes')]
    public function getPrefixes(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new DataModelPrefixesApiResource($dataModelVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_data_model_prefix_add')]
    public function addPrefix(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

        try {
            $parsed = $this->parseRequest(DataSpecificationPrefixApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationPrefixApiRequest);

            $bus->dispatch(
                new CreateDataModelPrefixCommand($dataModelVersion, $parsed->getPrefix(), $parsed->getUri())
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a data model prefix', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{prefix}', methods: ['POST'], name: 'api_data_model_prefix_update')]
    public function updatePrefix(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        #[MapEntity(mapping: ['prefix' => 'id'])]
        NamespacePrefix $prefix,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

        if ($prefix->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(DataSpecificationPrefixApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationPrefixApiRequest);

            $bus->dispatch(new UpdateDataModelPrefixCommand($prefix, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating a data model prefix',
                [
                    'exception' => $e,
                    'PrefixID' => $prefix->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{prefix}', methods: ['DELETE'], name: 'api_data_model_prefix_delete')]
    public function deletePrefix(
        #[MapEntity(mapping: ['model' => 'data_model', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
        #[MapEntity(mapping: ['prefix' => 'id'])]
        NamespacePrefix $prefix,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

        if ($prefix->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteDataModelPrefixCommand($prefix));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting a data model prefix',
                [
                    'exception' => $e,
                    'PrefixID' => $prefix->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

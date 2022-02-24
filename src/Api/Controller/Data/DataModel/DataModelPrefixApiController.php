<?php
declare(strict_types=1);

namespace App\Api\Controller\Data\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataModel\DataModelPrefixApiRequest;
use App\Api\Resource\Data\DataModel\DataModelPrefixesApiResource;
use App\Command\Data\DataModel\CreateDataModelPrefixCommand;
use App\Command\Data\DataModel\DeleteDataModelPrefixCommand;
use App\Command\Data\DataModel\UpdateDataModelPrefixCommand;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\NamespacePrefix;
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
 * @Route("/api/model/{model}/v/{version}/prefix")
 * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
 */
class DataModelPrefixApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_prefixes")
     */
    public function getPrefixes(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new DataModelPrefixesApiResource($dataModelVersion))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_model_prefix_add")
     */
    public function addPrefix(DataModelVersion $dataModelVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        try {
            $parsed = $this->parseRequest(DataModelPrefixApiRequest::class, $request);
            assert($parsed instanceof DataModelPrefixApiRequest);

            $bus->dispatch(new CreateDataModelPrefixCommand($dataModelVersion, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a data model prefix', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{prefix}", methods={"POST"}, name="api_model_prefix_update")
     * @ParamConverter("prefix", options={"mapping": {"prefix": "id"}})
     */
    public function updatePrefix(DataModelVersion $dataModelVersion, NamespacePrefix $prefix, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        if ($prefix->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(DataModelPrefixApiRequest::class, $request);
            assert($parsed instanceof DataModelPrefixApiRequest);

            $bus->dispatch(new UpdateDataModelPrefixCommand($prefix, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model prefix', [
                'exception' => $e,
                'PrefixID' => $prefix->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{prefix}", methods={"DELETE"}, name="api_model_prefix_delete")
     * @ParamConverter("prefix", options={"mapping": {"prefix": "id"}})
     */
    public function deletePrefix(DataModelVersion $dataModelVersion, NamespacePrefix $prefix, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        if ($prefix->getDataModelVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteDataModelPrefixCommand($prefix));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a data model prefix', [
                'exception' => $e,
                'PrefixID' => $prefix->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

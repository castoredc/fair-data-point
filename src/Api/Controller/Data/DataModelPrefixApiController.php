<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\DataModelPrefixApiRequest;
use App\Api\Resource\Data\DataModelPrefixesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateDataModelPrefixCommand;
use App\Message\Data\DeleteDataModelPrefixCommand;
use App\Message\Data\UpdateDataModelPrefixCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/model/{model}/prefix")
 * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
 */
class DataModelPrefixApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_prefixes")
     */
    public function getPrefixes(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return new JsonResponse((new DataModelPrefixesApiResource($dataModel))->toArray(), 200);
    }

    /**
     * @Route("", methods={"POST"}, name="api_model_prefix_add")
     */
    public function addPrefix(DataModel $dataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        try {
            /** @var DataModelPrefixApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelPrefixApiRequest::class, $request);

            $bus->dispatch(new CreateDataModelPrefixCommand($dataModel, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a data model prefix', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{prefix}", methods={"POST"}, name="api_model_prefix_update")
     * @ParamConverter("prefix", options={"mapping": {"prefix": "id", "dataModel": "model"}})
     */
    public function updatePrefix(NamespacePrefix $prefix, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $prefix->getDataModel());

        try {
            /** @var DataModelPrefixApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelPrefixApiRequest::class, $request);

            $bus->dispatch(new UpdateDataModelPrefixCommand($prefix, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model prefix', [
                'exception' => $e,
                'PrefixID' => $prefix->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{prefix}", methods={"DELETE"}, name="api_model_prefix_delete")
     * @ParamConverter("prefix", options={"mapping": {"prefix": "id", "dataModel": "model"}})
     */
    public function deletePrefix(NamespacePrefix $prefix, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $prefix->getDataModel());

        try {
            $bus->dispatch(new DeleteDataModelPrefixCommand($prefix));

            return new JsonResponse([], 200);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a data model prefix', [
                'exception' => $e,
                'PrefixID' => $prefix->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

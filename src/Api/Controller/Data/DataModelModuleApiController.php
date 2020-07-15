<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\DataModelModuleApiRequest;
use App\Api\Resource\Data\DataModelModulesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateDataModelModuleCommand;
use App\Message\Data\DeleteDataModelModuleCommand;
use App\Message\Data\UpdateDataModelModuleCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/model/{model}/v/{version}/module")
 * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
 */
class DataModelModuleApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_modules")
     */
    public function getModules(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new DataModelModulesApiResource($dataModelVersion))->toArray(), 200);
    }

    /**
     * @Route("", methods={"POST"}, name="api_model_module_add")
     */
    public function addModule(DataModelVersion $dataModelVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        try {
            /** @var DataModelModuleApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelModuleApiRequest::class, $request);

            $bus->dispatch(new CreateDataModelModuleCommand($dataModelVersion, $parsed->getTitle(), $parsed->getOrder()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model module', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{module}", methods={"POST"}, name="api_model_module_update")
     * @ParamConverter("module", options={"mapping": {"module": "id"}})
     */
    public function updateModule(DataModelVersion $dataModelVersion, DataModelModule $module, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getDataModel()->getDataModel());

        if ($module->getDataModel() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            /** @var DataModelModuleApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelModuleApiRequest::class, $request);

            $bus->dispatch(new UpdateDataModelModuleCommand($module, $parsed->getTitle(), $parsed->getOrder()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model module', [
                'exception' => $e,
                'ModuleID' => $module->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/{module}", methods={"DELETE"}, name="api_model_module_delete")
     * @ParamConverter("module", options={"mapping": {"module": "id"}})
     */
    public function deleteModule(DataModelVersion $dataModelVersion, DataModelModule $module, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getDataModel()->getDataModel());

        if ($module->getDataModel() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteDataModelModuleCommand($module));

            return new JsonResponse([], 200);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a data model module', [
                'exception' => $e,
                'ModuleID' => $module->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

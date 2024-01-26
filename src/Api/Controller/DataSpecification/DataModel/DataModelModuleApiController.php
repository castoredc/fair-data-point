<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataModel\DataModelModuleApiRequest;
use App\Api\Resource\Data\DataModel\DataModelModulesApiResource;
use App\Command\DataSpecification\DataModel\CreateDataModelModuleCommand;
use App\Command\DataSpecification\DataModel\DeleteDataModelModuleCommand;
use App\Command\DataSpecification\DataModel\UpdateDataModelModuleCommand;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
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
 * @Route("/api/model/{model}/v/{version}/module")
 * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
 */
class DataModelModuleApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_model_modules") */
    public function getModules(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new DataModelModulesApiResource($dataModelVersion))->toArray());
    }

    /** @Route("", methods={"POST"}, name="api_model_module_add") */
    public function addModule(DataModelVersion $dataModelVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        try {
            $parsed = $this->parseRequest(DataModelModuleApiRequest::class, $request);
            assert($parsed instanceof DataModelModuleApiRequest);

            $bus->dispatch(new CreateDataModelModuleCommand($dataModelVersion, $parsed->getTitle(), $parsed->getOrder(), $parsed->isRepeated(), $parsed->isDependent(), $parsed->getDependencies()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model module', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{module}", methods={"POST"}, name="api_model_module_update")
     * @ParamConverter("module", options={"mapping": {"module": "id"}})
     */
    public function updateModule(DataModelVersion $dataModelVersion, DataModelGroup $module, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

        if ($module->getVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(DataModelModuleApiRequest::class, $request);
            assert($parsed instanceof DataModelModuleApiRequest);

            $bus->dispatch(new UpdateDataModelModuleCommand($module, $parsed->getTitle(), $parsed->getOrder(), $parsed->isRepeated(), $parsed->isDependent(), $parsed->getDependencies()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model module', [
                'exception' => $e,
                'ModuleID' => $module->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{module}", methods={"DELETE"}, name="api_model_module_delete")
     * @ParamConverter("module", options={"mapping": {"module": "id"}})
     */
    public function deleteModule(DataModelVersion $dataModelVersion, DataModelGroup $module, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

        if ($module->getVersion() !== $dataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteDataModelModuleCommand($module));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a data model module', [
                'exception' => $e,
                'ModuleID' => $module->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

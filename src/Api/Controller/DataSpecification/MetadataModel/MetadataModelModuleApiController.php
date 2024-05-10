<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\MetadataModel\MetadataModelModuleApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelModulesApiResource;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelModuleCommand;
use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelModuleCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelModuleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
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
 * @Route("/api/metadata-model/{model}/v/{version}/module")
 * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
 */
class MetadataModelModuleApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_metadata_model_modules") */
    public function getModules(MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelModulesApiResource($metadataModelVersion))->toArray());
    }

    /** @Route("", methods={"POST"}, name="api_metadata_model_module_add") */
    public function addModule(MetadataModelVersion $metadataModelVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(MetadataModelModuleApiRequest::class, $request);
            assert($parsed instanceof MetadataModelModuleApiRequest);

            $bus->dispatch(
                new CreateMetadataModelModuleCommand(
                    $metadataModelVersion,
                    $parsed->getTitle(),
                    $parsed->getOrder(),
                    $parsed->getResourceType()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model module', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{module}", methods={"POST"}, name="api_metadata_model_module_update")
     * @ParamConverter("module", options={"mapping": {"module": "id"}})
     */
    public function updateModule(
        MetadataModelVersion $metadataModelVersion,
        MetadataModelGroup $module,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

        if ($module->getVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(MetadataModelModuleApiRequest::class, $request);
            assert($parsed instanceof MetadataModelModuleApiRequest);

            $bus->dispatch(
                new UpdateMetadataModelModuleCommand(
                    $module,
                    $parsed->getTitle(),
                    $parsed->getOrder(),
                    $parsed->getResourceType()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating a data model module',
                [
                    'exception' => $e,
                    'ModuleID' => $module->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{module}", methods={"DELETE"}, name="api_metadata_model_module_delete")
     * @ParamConverter("module", options={"mapping": {"module": "id"}})
     */
    public function deleteModule(
        MetadataModelVersion $metadataModelVersion,
        MetadataModelGroup $module,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted('edit', $module->getVersion()->getDataSpecification());

        if ($module->getVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelModuleCommand($module));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting a data model module',
                [
                    'exception' => $e,
                    'ModuleID' => $module->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

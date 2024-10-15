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
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata-model/{model}/v/{version}/module')]
class MetadataModelModuleApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_metadata_model_modules')]
    public function getModules(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelModulesApiResource($metadataModelVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_metadata_model_module_add')]
    public function addModule(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

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

    #[Route(path: '/{module}', methods: ['POST'], name: 'api_metadata_model_module_update')]
    public function updateModule(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['module' => 'id'])]
        MetadataModelGroup $module,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

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

    #[Route(path: '/{module}', methods: ['DELETE'], name: 'api_metadata_model_module_delete')]
    public function deleteModule(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['module' => 'id'])]
        MetadataModelGroup $module,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $module->getVersion()->getDataSpecification());

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

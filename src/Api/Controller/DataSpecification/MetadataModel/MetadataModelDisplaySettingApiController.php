<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\MetadataModel\MetadataModelDisplaySettingApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelDisplaySettingsApiResource;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelDisplaySettingCommand;
use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelDisplaySettingCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelDisplaySettingCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
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

#[Route(path: '/api/metadata-model/{model}/v/{version}/display')]
class MetadataModelDisplaySettingApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_metadata_model_displaySetting')]
    public function getDisplaySettings(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelDisplaySettingsApiResource($metadataModelVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_metadata_model_displaySetting_add')]
    public function addDisplaySetting(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(MetadataModelDisplaySettingApiRequest::class, $request);
            assert($parsed instanceof MetadataModelDisplaySettingApiRequest);

            $bus->dispatch(
                new CreateMetadataModelDisplaySettingCommand(
                    $metadataModelVersion,
                    $parsed->getTitle(),
                    $parsed->getOrder(),
                    $parsed->getNode(),
                    $parsed->getDisplayType(),
                    $parsed->getPosition(),
                    $parsed->getResourceType()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a displaySetting', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{displaySetting}', methods: ['POST'], name: 'api_metadata_model_displaySetting_update')]
    public function updateDisplaySetting(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['displaySetting' => 'id'])]
        MetadataModelDisplaySetting $displaySetting,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(
            DataSpecificationVoter::EDIT,
            $displaySetting->getMetadataModelVersion()->getDataSpecification()
        );

        if ($displaySetting->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(MetadataModelDisplaySettingApiRequest::class, $request);
            assert($parsed instanceof MetadataModelDisplaySettingApiRequest);

            $bus->dispatch(
                new UpdateMetadataModelDisplaySettingCommand(
                    $displaySetting,
                    $parsed->getTitle(),
                    $parsed->getOrder(),
                    $parsed->getNode(),
                    $parsed->getDisplayType(),
                    $parsed->getPosition()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating a data model displaySetting',
                [
                    'exception' => $e,
                    'DisplaySettingID' => $displaySetting->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{displaySetting}', methods: ['DELETE'], name: 'api_metadata_model_displaySetting_delete')]
    public function deleteDisplaySetting(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['displaySetting' => 'id'])]
        MetadataModelDisplaySetting $displaySetting,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(
            DataSpecificationVoter::EDIT,
            $displaySetting->getMetadataModelVersion()->getDataSpecification()
        );

        if ($displaySetting->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelDisplaySettingCommand($displaySetting));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting a displaySetting',
                [
                    'exception' => $e,
                    'DisplaySettingID' => $displaySetting->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

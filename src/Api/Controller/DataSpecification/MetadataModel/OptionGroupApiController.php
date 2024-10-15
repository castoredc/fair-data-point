<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\MetadataModel\OptionGroupApiRequest;
use App\Api\Resource\DataSpecification\Common\OptionGroupsApiResource;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelOptionGroupCommand;
use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelOptionGroupCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelOptionGroupCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
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

#[Route(path: '/api/metadata-model/{model}/v/{version}/option-group')]
class OptionGroupApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_metadata_model_option_groups')]
    public function getOptionGroups(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::USE, $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new OptionGroupsApiResource($metadataModelVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_metadata_model_option_group_add')]
    public function addOptionGroup(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(OptionGroupApiRequest::class, $request);
            assert($parsed instanceof OptionGroupApiRequest);

            $bus->dispatch(
                new CreateMetadataModelOptionGroupCommand(
                    $metadataModelVersion,
                    $parsed->getTitle(),
                    $parsed->getOptions(),
                    $parsed->getDescription(),
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding an option group', ['exception' => $e]);

            return new JsonResponse([$e->getCode(), $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{optionGroup}', methods: ['POST'], name: 'api_metadata_model_option_group_update')]
    public function updateOptionGroup(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['optionGroup' => 'id'])]
        MetadataModelOptionGroup $optionGroup,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        if ($optionGroup->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(OptionGroupApiRequest::class, $request);
            assert($parsed instanceof OptionGroupApiRequest);

            $bus->dispatch(
                new UpdateMetadataModelOptionGroupCommand(
                    $optionGroup,
                    $parsed->getTitle(),
                    $parsed->getOptions(),
                    $parsed->getDescription(),
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating an option group',
                [
                    'exception' => $e,
                    'OptionGroupID' => $optionGroup->getId(),
                ]
            );

            return new JsonResponse([$e->getCode(), $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{optionGroup}', methods: ['DELETE'], name: 'api_metadata_model_option_group_delete')]
    public function deletePrefix(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['optionGroup' => 'id'])]
        MetadataModelOptionGroup $optionGroup,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        if ($optionGroup->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelOptionGroupCommand($optionGroup));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting an option group',
                [
                    'exception' => $e,
                    'OptionGroupID' => $optionGroup->getId(),
                ]
            );

            return new JsonResponse([$e->getCode(), $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

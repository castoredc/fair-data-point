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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata-model/{model}/v/{version}/option-group")
 * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
 */
class OptionGroupApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_metadata_model_option_groups") */
    public function getOptionGroups(MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::USE, $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new OptionGroupsApiResource($metadataModelVersion))->toArray());
    }

    /** @Route("", methods={"POST"}, name="api_metadata_model_option_group_add") */
    public function addOptionGroup(MetadataModelVersion $metadataModelVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(OptionGroupApiRequest::class, $request);
            assert($parsed instanceof OptionGroupApiRequest);

            $bus->dispatch(new CreateMetadataModelOptionGroupCommand(
                $metadataModelVersion,
                $parsed->getTitle(),
                $parsed->getDescription(),
                $parsed->getOptions()
            ));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding an option group', ['exception' => $e]);

            return new JsonResponse([$e->getCode(), $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{optionGroup}", methods={"POST"}, name="api_metadata_model_option_group_update")
     * @ParamConverter("optionGroup", options={"mapping": {"optionGroup": "id"}})
     */
    public function updateOptionGroup(MetadataModelVersion $metadataModelVersion, MetadataModelOptionGroup $optionGroup, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        if ($optionGroup->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(OptionGroupApiRequest::class, $request);
            assert($parsed instanceof OptionGroupApiRequest);

            $bus->dispatch(new UpdateMetadataModelOptionGroupCommand(
                $optionGroup,
                $parsed->getTitle(),
                $parsed->getDescription(),
                $parsed->getOptions()
            ));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating an option group', [
                'exception' => $e,
                'OptionGroupID' => $optionGroup->getId(),
            ]);

            return new JsonResponse([$e->getCode(), $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{optionGroup}", methods={"DELETE"}, name="api_metadata_model_option_group_delete")
     * @ParamConverter("optionGroup", options={"mapping": {"optionGroup": "id"}})
     */
    public function deletePrefix(MetadataModelVersion $metadataModelVersion, MetadataModelOptionGroup $optionGroup, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        if ($optionGroup->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelOptionGroupCommand($optionGroup));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting an option group', [
                'exception' => $e,
                'OptionGroupID' => $optionGroup->getId(),
            ]);

            return new JsonResponse([$e->getCode(), $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\MetadataModel\MetadataModelFieldApiRequest;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelFieldCommand;
use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelFieldCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelFieldCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
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
use function dump;

/**
 * @Route("/api/metadata-model/{model}/v/{version}/form/{form}/field")
 * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
 * @ParamConverter("form", options={"mapping": {"form": "id"}})
 */
class MetadataModelFieldApiController extends ApiController
{
    /** @Route("", methods={"POST"}, name="api_metadata_model_field_add") */
    public function addField(MetadataModelVersion $metadataModelVersion, MetadataModelForm $form, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(MetadataModelFieldApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFieldApiRequest);

            $bus->dispatch(new CreateMetadataModelFieldCommand($form, $parsed->getTitle(), $parsed->getDescription(), $parsed->getOrder(), $parsed->getNode(), $parsed->getFieldType(), $parsed->getOptionGroup()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a field', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{field}", methods={"POST"}, name="api_metadata_model_field_update")
     * @ParamConverter("field", options={"mapping": {"field": "id"}})
     */
    public function updateField(MetadataModelVersion $metadataModelVersion, MetadataModelForm $form, MetadataModelField $field, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $form->getMetadataModelVersion()->getDataSpecification());

        if ($field->getForm() !== $form || $form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(MetadataModelFieldApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFieldApiRequest);

            $bus->dispatch(new UpdateMetadataModelFieldCommand($field, $parsed->getTitle(), $parsed->getDescription(), $parsed->getOrder(), $parsed->getNode(), $parsed->getFieldType(), $parsed->getOptionGroup()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model field', [
                'exception' => $e,
                'FieldID' => $field->getId(),
            ]);

            dump($e);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{field}", methods={"DELETE"}, name="api_metadata_model_field_delete")
     * @ParamConverter("field", options={"mapping": {"field": "id"}})
     */
    public function deleteField(MetadataModelVersion $metadataModelVersion, MetadataModelForm $form, MetadataModelField $field, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $form->getMetadataModelVersion()->getDataSpecification());

        if ($field->getForm() !== $form || $form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelFieldCommand($field));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a field', [
                'exception' => $e,
                'FieldID' => $field->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

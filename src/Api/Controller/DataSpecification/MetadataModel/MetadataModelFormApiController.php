<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\MetadataModel\MetadataModelFormApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelFormsApiResource;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelFormCommand;
use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelFormCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelFormCommand;
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

/**
 * @Route("/api/metadata-model/{model}/v/{version}/form")
 * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
 */
class MetadataModelFormApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_metadata_model_forms") */
    public function getForms(MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelFormsApiResource($metadataModelVersion))->toArray());
    }

    /** @Route("", methods={"POST"}, name="api_metadata_model_form_add") */
    public function addForm(MetadataModelVersion $metadataModelVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(MetadataModelFormApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFormApiRequest);

            $bus->dispatch(new CreateMetadataModelFormCommand($metadataModelVersion, $parsed->getTitle(), $parsed->getOrder()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a form', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{form}", methods={"POST"}, name="api_metadata_model_form_update")
     * @ParamConverter("form", options={"mapping": {"form": "id"}})
     */
    public function updateForm(MetadataModelVersion $metadataModelVersion, MetadataModelForm $form, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $form->getMetadataModelVersion()->getDataSpecification());

        if ($form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(MetadataModelFormApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFormApiRequest);

            $bus->dispatch(new UpdateMetadataModelFormCommand($form, $parsed->getTitle(), $parsed->getOrder()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model form', [
                'exception' => $e,
                'FormID' => $form->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{form}", methods={"DELETE"}, name="api_metadata_model_form_delete")
     * @ParamConverter("form", options={"mapping": {"form": "id"}})
     */
    public function deleteForm(MetadataModelVersion $metadataModelVersion, MetadataModelForm $form, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $form->getMetadataModelVersion()->getDataSpecification());

        if ($form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelFormCommand($form));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while deleting a form', [
                'exception' => $e,
                'FormID' => $form->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

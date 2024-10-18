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
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata-model/{model}/v/{version}/form')]
class MetadataModelFormApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_metadata_model_forms')]
    public function getForms(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelFormsApiResource($metadataModelVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_metadata_model_form_add')]
    public function addForm(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(MetadataModelFormApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFormApiRequest);

            $bus->dispatch(
                new CreateMetadataModelFormCommand(
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
            $this->logger->critical('An error occurred while creating a form', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{form}', methods: ['POST'], name: 'api_metadata_model_form_update')]
    public function updateForm(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['form' => 'id'])]
        MetadataModelForm $form,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(
            DataSpecificationVoter::EDIT,
            $form->getMetadataModelVersion()->getDataSpecification()
        );

        if ($form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(MetadataModelFormApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFormApiRequest);

            $bus->dispatch(
                new UpdateMetadataModelFormCommand(
                    $form,
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
                'An error occurred while updating a data model form',
                [
                    'exception' => $e,
                    'FormID' => $form->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{form}', methods: ['DELETE'], name: 'api_metadata_model_form_delete')]
    public function deleteForm(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['form' => 'id'])]
        MetadataModelForm $form,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(
            DataSpecificationVoter::EDIT,
            $form->getMetadataModelVersion()->getDataSpecification()
        );

        if ($form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelFormCommand($form));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting a form',
                [
                    'exception' => $e,
                    'FormID' => $form->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

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
use App\Exception\DataSpecification\MetadataModel\NodeAlreadyUsed;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata-model/{model}/v/{version}/form/{form}/field')]
class MetadataModelFieldApiController extends ApiController
{
    #[Route(path: '', methods: ['POST'], name: 'api_metadata_model_field_add')]
    public function addField(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['form' => 'id'])]
        MetadataModelForm $form,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(MetadataModelFieldApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFieldApiRequest);

            $this->bus->dispatch(
                new CreateMetadataModelFieldCommand(
                    $form,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getOrder(),
                    $parsed->getNode(),
                    $parsed->getFieldType(),
                    $parsed->getOptionGroup(),
                    $form->getResourceType(),
                    $parsed->getIsRequired()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NodeAlreadyUsed) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while creating a field', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{field}', methods: ['POST'], name: 'api_metadata_model_field_update')]
    public function updateField(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['form' => 'id'])]
        MetadataModelForm $form,
        #[MapEntity(mapping: ['field' => 'id'])]
        MetadataModelField $field,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(
            DataSpecificationVoter::EDIT,
            $form->getMetadataModelVersion()->getDataSpecification()
        );

        if ($field->getForm() !== $form || $form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(MetadataModelFieldApiRequest::class, $request);
            assert($parsed instanceof MetadataModelFieldApiRequest);

            $this->bus->dispatch(
                new UpdateMetadataModelFieldCommand(
                    $field,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getOrder(),
                    $parsed->getNode(),
                    $parsed->getFieldType(),
                    $parsed->getOptionGroup(),
                    $form->getResourceType(),
                    $parsed->getIsRequired()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NodeAlreadyUsed) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical(
                'An error occurred while updating a data model field',
                [
                    'exception' => $e,
                    'FieldID' => $field->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{field}', methods: ['DELETE'], name: 'api_metadata_model_field_delete')]
    public function deleteField(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['form' => 'id'])]
        MetadataModelForm $form,
        #[MapEntity(mapping: ['field' => 'id'])]
        MetadataModelField $field,
    ): Response {
        $this->denyAccessUnlessGranted(
            DataSpecificationVoter::EDIT,
            $form->getMetadataModelVersion()->getDataSpecification()
        );

        if ($field->getForm() !== $form || $form->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->bus->dispatch(new DeleteMetadataModelFieldCommand($field));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical(
                'An error occurred while deleting a field',
                [
                    'exception' => $e,
                    'FieldID' => $field->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

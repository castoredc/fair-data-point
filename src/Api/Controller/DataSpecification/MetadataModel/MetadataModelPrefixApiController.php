<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\Common\DataSpecificationPrefixApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelPrefixesApiResource;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelPrefixCommand;
use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelPrefixCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelPrefixCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;
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

#[Route(path: '/api/metadata-model/{model}/v/{version}/prefix')]
class MetadataModelPrefixApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_metadata_model_prefixes')]
    public function getPrefixes(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelPrefixesApiResource($metadataModelVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_metadata_model_prefix_add')]
    public function addPrefix(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        try {
            $parsed = $this->parseRequest(DataSpecificationPrefixApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationPrefixApiRequest);

            $bus->dispatch(
                new CreateMetadataModelPrefixCommand($metadataModelVersion, $parsed->getPrefix(), $parsed->getUri())
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding a data model prefix', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{prefix}', methods: ['POST'], name: 'api_metadata_model_prefix_update')]
    public function updatePrefix(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['prefix' => 'id'])]
        NamespacePrefix $prefix,
        Request $request,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        if ($prefix->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = $this->parseRequest(DataSpecificationPrefixApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationPrefixApiRequest);

            $bus->dispatch(new UpdateMetadataModelPrefixCommand($prefix, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating a data model prefix',
                [
                    'exception' => $e,
                    'PrefixID' => $prefix->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{prefix}', methods: ['DELETE'], name: 'api_metadata_model_prefix_delete')]
    public function deletePrefix(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
        #[MapEntity(mapping: ['prefix' => 'id'])]
        NamespacePrefix $prefix,
        MessageBusInterface $bus,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $metadataModelVersion->getMetadataModel());

        if ($prefix->getMetadataModelVersion() !== $metadataModelVersion) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DeleteMetadataModelPrefixCommand($prefix));

            return new JsonResponse([]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while deleting a data model prefix',
                [
                    'exception' => $e,
                    'PrefixID' => $prefix->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\Common\DataSpecificationVersionApiRequest;
use App\Api\Request\DataSpecification\Common\DataSpecificationVersionTypeApiRequest;
use App\Api\Request\DataSpecification\MetadataModel\MetadataModelApiRequest;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelApiResource;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelsApiResource;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelVersionApiResource;
use App\Api\Resource\DataSpecification\MetadataModel\MetadataModelVersionExportApiResource;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelCommand;
use App\Command\DataSpecification\MetadataModel\CreateMetadataModelVersionCommand;
use App\Command\DataSpecification\MetadataModel\FindMetadataModelsByUserCommand;
use App\Command\DataSpecification\MetadataModel\GetMetadataModelRDFPreviewCommand;
use App\Command\DataSpecification\MetadataModel\ImportMetadataModelVersionCommand;
use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Exception\ApiRequestParseError;
use App\Exception\DataSpecification\MetadataModel\InvalidMetadataModelVersion;
use App\Exception\SessionTimedOut;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Exception\Upload\NoFileSpecified;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use App\Security\User;
use Cocur\Slugify\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function sprintf;
use const JSON_PRETTY_PRINT;

/** @Route("/api/model") */
class MetadataModelApiController extends ApiController
{
    /** @Route("", methods={"POST"}, name="api_metadata_model_add") */
    public function addMetadataModel(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(MetadataModelApiRequest::class, $request);
            assert($parsed instanceof MetadataModelApiRequest);

            $envelope = $bus->dispatch(new CreateMetadataModelCommand($parsed->getTitle(), $parsed->getDescription()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new MetadataModelApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/my", methods={"GET"}, name="api_my_data_models") */
    public function myMetadataModels(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        try {
            $envelope = $bus->dispatch(new FindMetadataModelsByUserCommand($user));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new MetadataModelsApiResource($handledStamp->getResult()))->toArray());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof SessionTimedOut) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNAUTHORIZED);
            }

            $this->logger->critical('An error occurred while loading the data models', ['exception' => $e]);
        }

        return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @Route("/{model}", methods={"GET"}, name="api_metadata_model")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     */
    public function dataModel(MetadataModel $metadataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModel);

        return $this->getResponse(
            new MetadataModelApiResource($metadataModel),
            $metadataModel,
            [DataSpecificationVoter::VIEW, DataSpecificationVoter::EDIT, DataSpecificationVoter::MANAGE]
        );
    }

    /**
     * @Route("/{model}", methods={"POST"}, name="api_metadata_model_update")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     */
    public function updateMetadataModel(MetadataModel $metadataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModel);

        try {
            $parsed = $this->parseRequest(MetadataModelApiRequest::class, $request);
            assert($parsed instanceof MetadataModelApiRequest);

            $bus->dispatch(new UpdateMetadataModelCommand($metadataModel, $parsed->getTitle(), $parsed->getDescription()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}/v/{version}", methods={"GET"}, name="api_metadata_model_version")
     * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
     */
    public function dataModelVersion(MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new MetadataModelVersionApiResource($metadataModelVersion))->toArray());
    }

    /**
     * @Route("/{model}/v", methods={"POST"}, name="api_metadata_model_version_create")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     */
    public function createMetadataModelVersion(MetadataModel $metadataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModel);

        try {
            $parsed = $this->parseRequest(DataSpecificationVersionTypeApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationVersionTypeApiRequest);

            $envelope = $bus->dispatch(new CreateMetadataModelVersionCommand($metadataModel, $parsed->getVersionType()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new MetadataModelVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model version', [
                'exception' => $e,
                'dataModel' => $metadataModel->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}/import", methods={"POST"}, name="api_metadata_model_import")
     * @ParamConverter("metadataModel", options={"mapping": {"model": "id"}})
     */
    public function importMetadataModelVersion(MetadataModel $metadataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModel);

        $file = $request->files->get('file');
        assert($file instanceof UploadedFile || $file === null);

        try {
            if ($file === null) {
                throw new NoFileSpecified();
            }

            $parsed = $this->parseRequest(DataSpecificationVersionApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationVersionApiRequest);

            $envelope = $bus->dispatch(new ImportMetadataModelVersionCommand($metadataModel, $file, $parsed->getVersion()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new MetadataModelVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (NoFileSpecified $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof InvalidFile || $e instanceof EmptyFile || $e instanceof InvalidJSON || $e instanceof InvalidMetadataModelVersion) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical('An error occurred while importing a data model', [
                'exception' => $e,
                'dataModel' => $metadataModel->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}/v/{version}/export", methods={"GET"}, name="api_metadata_model_version_export")
     * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
     */
    public function exportMetadataModelVersion(MetadataModelVersion $metadataModelVersion, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadataModelVersion->getMetadataModel());

        $response = new JsonResponse((new MetadataModelVersionExportApiResource($metadataModelVersion))->toArray());

        $slugify = new Slugify();
        $name = sprintf('%s - %s.json', $slugify->slugify($metadataModelVersion->getMetadataModel()->getTitle()), $metadataModelVersion->getVersion()->getValue());
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @Route("/{model}/v/{version}/rdf", methods={"GET"}, name="api_metadata_model_rdf_preview")
     * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
     */
    public function dataModelRDFPreview(MetadataModelVersion $metadataModelVersion, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        $envelope = $bus->dispatch(new GetMetadataModelRDFPreviewCommand($metadataModelVersion));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

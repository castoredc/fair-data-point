<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataModel\DataModelApiRequest;
use App\Api\Request\Data\DataModel\DataModelVersionApiRequest;
use App\Api\Request\Data\DataModel\DataModelVersionTypeApiRequest;
use App\Api\Resource\Data\DataModel\DataModelApiResource;
use App\Api\Resource\Data\DataModel\DataModelsApiResource;
use App\Api\Resource\Data\DataModel\DataModelVersionApiResource;
use App\Api\Resource\Data\DataModel\DataModelVersionExportApiResource;
use App\Command\DataSpecification\DataModel\CreateDataModelCommand;
use App\Command\DataSpecification\DataModel\CreateDataModelVersionCommand;
use App\Command\DataSpecification\DataModel\FindDataModelsByUserCommand;
use App\Command\DataSpecification\DataModel\GetDataModelRDFPreviewCommand;
use App\Command\DataSpecification\DataModel\ImportDataModelVersionCommand;
use App\Command\DataSpecification\DataModel\UpdateDataModelCommand;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Exception\ApiRequestParseError;
use App\Exception\DataSpecification\DataModel\InvalidDataModelVersion;
use App\Exception\SessionTimedOut;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Exception\Upload\NoFileSpecified;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use App\Security\User;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use function assert;
use function sprintf;
use const JSON_PRETTY_PRINT;

/** @Route("/api/model") */
class DataModelApiController extends ApiController
{
    /** @Route("", methods={"POST"}, name="api_model_add") */
    public function addDataModel(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(DataModelApiRequest::class, $request);
            assert($parsed instanceof DataModelApiRequest);

            $envelope = $bus->dispatch(new CreateDataModelCommand($parsed->getTitle(), $parsed->getDescription()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataModelApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/my", methods={"GET"}, name="api_my_data_models") */
    public function myDataModels(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        try {
            $envelope = $bus->dispatch(new FindDataModelsByUserCommand($user));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataModelsApiResource($handledStamp->getResult()))->toArray());
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
     * @Route("/{model}", methods={"GET"}, name="api_model")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function dataModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return $this->getResponse(
            new DataModelApiResource($dataModel),
            $dataModel,
            [DataSpecificationVoter::VIEW, DataSpecificationVoter::EDIT, DataSpecificationVoter::MANAGE]
        );
    }

    /**
     * @Route("/{model}", methods={"POST"}, name="api_model_update")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function updateDataModel(DataModel $dataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        try {
            $parsed = $this->parseRequest(DataModelApiRequest::class, $request);
            assert($parsed instanceof DataModelApiRequest);

            $bus->dispatch(new UpdateDataModelCommand($dataModel, $parsed->getTitle(), $parsed->getDescription()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}/v/{version}", methods={"GET"}, name="api_model_version")
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
     */
    public function dataModelVersion(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new DataModelVersionApiResource($dataModelVersion))->toArray());
    }

    /**
     * @Route("/{model}/v", methods={"POST"}, name="api_model_version_create")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function createDataModelVersion(DataModel $dataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        try {
            $parsed = $this->parseRequest(DataModelVersionTypeApiRequest::class, $request);
            assert($parsed instanceof DataModelVersionTypeApiRequest);

            $envelope = $bus->dispatch(new CreateDataModelVersionCommand($dataModel, $parsed->getVersionType()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataModelVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model version', [
                'exception' => $e,
                'dataModel' => $dataModel->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}/import", methods={"POST"}, name="api_model_import")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function importDataModelVersion(DataModel $dataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        $file = $request->files->get('file');
        assert($file instanceof UploadedFile || $file === null);

        try {
            if ($file === null) {
                throw new NoFileSpecified();
            }

            $parsed = $this->parseRequest(DataModelVersionApiRequest::class, $request);
            assert($parsed instanceof DataModelVersionApiRequest);

            $envelope = $bus->dispatch(new ImportDataModelVersionCommand($dataModel, $file, $parsed->getVersion()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataModelVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (NoFileSpecified $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof InvalidFile || $e instanceof EmptyFile || $e instanceof InvalidJSON || $e instanceof InvalidDataModelVersion) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical('An error occurred while importing a data model', [
                'exception' => $e,
                'dataModel' => $dataModel->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}/v/{version}/export", methods={"GET"}, name="api_model_version_export")
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
     */
    public function exportDataModelVersion(DataModelVersion $dataModelVersion, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModelVersion->getDataModel());

        $response = new JsonResponse((new DataModelVersionExportApiResource($dataModelVersion))->toArray());

        $slugify = new Slugify();
        $name = sprintf('%s - %s.json', $slugify->slugify($dataModelVersion->getDataModel()->getTitle()), $dataModelVersion->getVersion()->getValue());
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @Route("/{model}/v/{version}/rdf", methods={"GET"}, name="api_model_rdf_preview")
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
     */
    public function dataModelRDFPreview(DataModelVersion $dataModelVersion, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        $envelope = $bus->dispatch(new GetDataModelRDFPreviewCommand($dataModelVersion));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

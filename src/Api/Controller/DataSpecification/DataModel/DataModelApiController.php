<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\Common\DataSpecificationVersionApiRequest;
use App\Api\Request\DataSpecification\Common\DataSpecificationVersionTypeApiRequest;
use App\Api\Request\DataSpecification\DataModel\DataModelApiRequest;
use App\Api\Resource\DataSpecification\DataModel\DataModelApiResource;
use App\Api\Resource\DataSpecification\DataModel\DataModelsApiResource;
use App\Api\Resource\DataSpecification\DataModel\DataModelVersionApiResource;
use App\Api\Resource\DataSpecification\DataModel\DataModelVersionExportApiResource;
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
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

#[Route(path: '/api/data-model')]
class DataModelApiController extends ApiController
{
    #[Route(path: '', methods: ['POST'], name: 'api_data_model_add')]
    public function addDataModel(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(DataModelApiRequest::class, $request);
            assert($parsed instanceof DataModelApiRequest);

            $envelope = $this->bus->dispatch(new CreateDataModelCommand($parsed->getTitle(), $parsed->getDescription()));

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

    #[Route(path: '/my', methods: ['GET'], name: 'api_my_data_models')]
    public function myDataModels(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        try {
            $envelope = $this->bus->dispatch(new FindDataModelsByUserCommand($user));

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

    #[Route(path: '/{model}', methods: ['GET'], name: 'api_data_model')]
    public function dataModel(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return $this->getResponse(
            new DataModelApiResource($dataModel),
            $dataModel,
            [DataSpecificationVoter::USE, DataSpecificationVoter::VIEW, DataSpecificationVoter::EDIT, DataSpecificationVoter::MANAGE]
        );
    }

    #[Route(path: '/{model}', methods: ['POST'], name: 'api_data_model_update')]
    public function updateDataModel(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModel);

        try {
            $parsed = $this->parseRequest(DataModelApiRequest::class, $request);
            assert($parsed instanceof DataModelApiRequest);

            $this->bus->dispatch(new UpdateDataModelCommand($dataModel, $parsed->getTitle(), $parsed->getDescription()));

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a data model', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{model}/v/{version}', methods: ['GET'], name: 'api_data_model_version')]
    public function dataModelVersion(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new DataModelVersionApiResource($dataModelVersion))->toArray());
    }

    #[Route(path: '/{model}/v', methods: ['POST'], name: 'api_data_model_version_create')]
    public function createDataModelVersion(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModel);

        try {
            $parsed = $this->parseRequest(DataSpecificationVersionTypeApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationVersionTypeApiRequest);

            $envelope = $this->bus->dispatch(new CreateDataModelVersionCommand($dataModel, $parsed->getVersionType()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataModelVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while creating a data model version',
                [
                    'exception' => $e,
                    'dataModel' => $dataModel->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{model}/import', methods: ['POST'], name: 'api_data_model_import')]
    public function importDataModelVersion(
        #[MapEntity(mapping: ['model' => 'id'])]
        DataModel $dataModel,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModel);

        $file = $request->files->get('file');
        assert($file instanceof UploadedFile || $file === null);

        try {
            if ($file === null) {
                throw new NoFileSpecified();
            }

            $parsed = $this->parseRequest(DataSpecificationVersionApiRequest::class, $request);
            assert($parsed instanceof DataSpecificationVersionApiRequest);

            $envelope = $this->bus->dispatch(new ImportDataModelVersionCommand($dataModel, $file, $parsed->getVersion()));

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

            $this->logger->critical(
                'An error occurred while importing a data model',
                [
                    'exception' => $e,
                    'dataModel' => $dataModel->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{model}/v/{version}/export', methods: ['GET'], name: 'api_data_model_version_export')]
    public function exportDataModelVersion(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataModelVersion->getDataModel());

        $response = new JsonResponse((new DataModelVersionExportApiResource($dataModelVersion))->toArray());

        $slugify = new Slugify();
        $name = sprintf(
            '%s - %s.json',
            $slugify->slugify($dataModelVersion->getDataModel()->getTitle()),
            $dataModelVersion->getVersion()->getValue()
        );
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route(path: '/{model}/v/{version}/rdf', methods: ['GET'], name: 'api_data_model_rdf_preview')]
    public function dataModelRDFPreview(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        $envelope = $this->bus->dispatch(new GetDataModelRDFPreviewCommand($dataModelVersion));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

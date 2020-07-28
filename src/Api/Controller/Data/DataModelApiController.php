<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\DataModelApiRequest;
use App\Api\Request\Data\DataModelVersionApiRequest;
use App\Api\Resource\Data\DataModelApiResource;
use App\Api\Resource\Data\DataModelsApiResource;
use App\Api\Resource\Data\DataModelVersionApiResource;
use App\Api\Resource\Data\DataModelVersionExportApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateDataModelCommand;
use App\Message\Data\CreateDataModelVersionCommand;
use App\Message\Data\GetDataModelRDFPreviewCommand;
use App\Message\Data\GetDataModelsCommand;
use Cocur\Slugify\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use const JSON_PRETTY_PRINT;
use function sprintf;

/**
 * @Route("/api/model")
 */
class DataModelApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_models")
     */
    public function dataModels(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $envelope = $bus->dispatch(new GetDataModelsCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new DataModelsApiResource($handledStamp->getResult()))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_model_add")
     */
    public function addDataModel(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            /** @var DataModelApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelApiRequest::class, $request);

            $envelope = $bus->dispatch(new CreateDataModelCommand($parsed->getTitle(), $parsed->getDescription()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse((new DataModelApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data model', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{model}", methods={"GET"}, name="api_model")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function dataModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return new JsonResponse((new DataModelApiResource($dataModel))->toArray());
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
            /** @var DataModelVersionApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelVersionApiRequest::class, $request);

            $envelope = $bus->dispatch(new CreateDataModelVersionCommand($dataModel, $parsed->getVersionType()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

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
     * @Route("/{model}/v/{version}/export", methods={"GET"}, name="api_model_version_export")
     * @ParamConverter("dataModelVersion", options={"mapping": {"model": "data_model", "version": "id"}})
     */
    public function exportDataModelVersion(DataModelVersion $dataModelVersion, Request $request, MessageBusInterface $bus): Response
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

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

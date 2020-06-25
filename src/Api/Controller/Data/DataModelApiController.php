<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\DataModelApiRequest;
use App\Api\Resource\Data\DataModelApiResource;
use App\Api\Resource\Data\DataModelsApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModel;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateDataModelCommand;
use App\Message\Data\GetDataModelRDFPreviewCommand;
use App\Message\Data\GetDataModelsCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/{model}", name="api_model")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function dataModel(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return new JsonResponse((new DataModelApiResource($dataModel))->toArray());
    }

    /**
     * @Route("/{model}/rdf", name="api_model_rdf_preview")
     * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
     */
    public function dataModelRDFPreview(DataModel $dataModel, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        $envelope = $bus->dispatch(new GetDataModelRDFPreviewCommand($dataModel));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

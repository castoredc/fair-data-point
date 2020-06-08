<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\DataModelPrefixApiRequest;
use App\Api\Resource\Data\DataModelPrefixesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModel;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateDataModelPrefixCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/model/{model}/prefix")
 * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
 */
class DataModelPrefixApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_prefixes")
     */
    public function getPrefixes(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return new JsonResponse((new DataModelPrefixesApiResource($dataModel))->toArray(), 200);
    }

    /**
     * @Route("/add", methods={"POST"}, name="api_model_prefix_add")
     */
    public function addPrefix(DataModel $dataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        try {
            /** @var DataModelPrefixApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelPrefixApiRequest::class, $request);

            $bus->dispatch(new CreateDataModelPrefixCommand($dataModel, $parsed->getPrefix(), $parsed->getUri()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

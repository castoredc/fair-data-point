<?php
declare(strict_types=1);

namespace App\Api\Controller\Data;

use App\Api\Request\Data\DataModelModuleApiRequest;
use App\Api\Resource\Data\DataModelModulesApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\DataModel\DataModel;
use App\Exception\ApiRequestParseError;
use App\Message\Data\CreateDataModelModuleCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/model/{model}/module")
 * @ParamConverter("dataModel", options={"mapping": {"model": "id"}})
 */
class DataModelModuleApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_model_modules")
     */
    public function getModules(DataModel $dataModel): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModel);

        return new JsonResponse((new DataModelModulesApiResource($dataModel))->toArray(), 200);
    }

    /**
     * @Route("/add", methods={"POST"}, name="api_distribution_rdf_module_add")
     */
    public function addRdfModule(DataModel $dataModel, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataModel);

        try {
            /** @var DataModelModuleApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelModuleApiRequest::class, $request);

            $bus->dispatch(new CreateDataModelModuleCommand($dataModel, $parsed->getTitle(), $parsed->getOrder()));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

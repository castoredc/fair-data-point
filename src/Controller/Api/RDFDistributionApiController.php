<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Distribution\RDFDistributionModuleApiRequest;
use App\Api\Request\Distribution\RDFDistributionPrefixApiRequest;
use App\Api\Resource\Distribution\RDFDistributionPrefixesResource;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Message\Distribution\AddRDFDistributionModuleCommand;
use App\Message\Distribution\AddRDFDistributionPrefixCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class RDFDistributionApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/rdf/module/add", methods={"POST"}, name="api_distribution_rdf_module_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function addRdfModule(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution) {
            throw $this->createNotFoundException();
        }

        try {
            /** @var RDFDistributionModuleApiRequest $parsed */
            $parsed = $this->parseRequest(RDFDistributionModuleApiRequest::class, $request);

            $bus->dispatch(new AddRDFDistributionModuleCommand($parsed->getTitle(), $parsed->getOrder(), $contents));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/rdf/prefix", methods={"GET"}, name="api_distribution_rdf_prefixes")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function getPrefixes(Catalog $catalog, Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('view', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new RDFDistributionPrefixesResource($contents))->toArray(), 200);
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/rdf/prefix/add", methods={"POST"}, name="api_distribution_rdf_prefixes_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function addPrefix(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution) {
            throw $this->createNotFoundException();
        }

        try {
            /** @var RDFDistributionPrefixApiRequest $parsed */
            $parsed = $this->parseRequest(RDFDistributionPrefixApiRequest::class, $request);

            $bus->dispatch(new AddRDFDistributionPrefixCommand($parsed->getPrefix(), $parsed->getUri(), $contents));

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Distribution\DistributionApiRequest;
use App\Api\Request\Distribution\DistributionContentApiRequest;
use App\Api\Request\Distribution\RDFDistributionModuleApiRequest;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Distribution\DistributionContentApiResource;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Exception\GroupedApiRequestParseError;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\AddCSVDistributionContentCommand;
use App\Message\Distribution\AddDistributionCommand;
use App\Message\Distribution\AddDistributionContentsCommand;
use App\Message\Distribution\AddRDFDistributionModuleCommand;
use App\Message\Distribution\ClearDistributionContentCommand;
use App\Message\Distribution\CreateDistributionDatabaseCommand;
use App\Message\Distribution\UpdateDistributionCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
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
            return new JsonResponse([$e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage()], 500);
        }
    }
}

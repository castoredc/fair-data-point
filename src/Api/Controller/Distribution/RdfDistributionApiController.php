<?php
declare(strict_types=1);

namespace App\Api\Controller\Distribution;

use App\Api\Request\Distribution\DataModelMappingApiRequest;
use App\Api\Resource\Distribution\DataModelMappingApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\RDF\DataModelMapping;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\PaginatedResultCollection;
use App\Exception\ApiRequestParseError;
use App\Exception\InvalidDistributionType;
use App\Exception\MappingAlreadyExists;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Message\Distribution\CreateDataModelMappingCommand;
use App\Message\Distribution\GetDataModelMappingCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/dataset/{dataset}/distribution/{distribution}/contents/rdf")
 * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
 * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
 */
class RdfDistributionApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_distribution_contents_rdf")
     */
    public function distributionRdfContents(Dataset $dataset, Distribution $distribution, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        $contents = $distribution->getContents();

        if (! $contents instanceof RDFDistribution) {
            throw new InvalidDistributionType();
        }

        $envelope = $bus->dispatch(new GetDataModelMappingCommand($contents));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        /** @var PaginatedResultCollection $results */
        $results = $handledStamp->getResult();

        return new JsonResponse((new PaginatedApiResource(DataModelMappingApiResource::class, $results))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_distribution_contents_rdf_add")
     */
    public function addMapping(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        $contents = $distribution->getContents();

        if (! $contents instanceof RDFDistribution) {
            throw new InvalidDistributionType();
        }

        try {
            /** @var DataModelMappingApiRequest $parsed */
            $parsed = $this->parseRequest(DataModelMappingApiRequest::class, $request);

            $envelope = $bus->dispatch(new CreateDataModelMappingCommand($contents, $parsed->getNode(), $parsed->getElement()));

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var DataModelMapping $result */
            $result = $handledStamp->getResult();

            return new JsonResponse((new DataModelMappingApiResource($result))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), 403);
            }

            if ($e instanceof MappingAlreadyExists) {
                return new JsonResponse($e->toArray(), 409);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), 404);
            }

            $this->logger->critical('An error occurred while adding a mapping to an RDF distribution', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

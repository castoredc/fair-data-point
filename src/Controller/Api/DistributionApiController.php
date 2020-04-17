<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Distribution\DistributionApiRequest;
use App\Api\Request\Distribution\DistributionContentApiRequest;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Distribution\DistributionContentApiResource;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\CSVDistribution\CSVDistribution;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Exception\ApiRequestParseError;
use App\Exception\GroupedApiRequestParseError;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\AddCSVDistributionContentCommand;
use App\Message\Distribution\AddDistributionCommand;
use App\Message\Distribution\ClearDistributionContentCommand;
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

class DistributionApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}", methods={"GET"}, name="api_distribution")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distribution(Catalog $catalog, Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionApiResource($distribution, $this->isGranted('ROLE_ADMIN')))->toArray());
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/contents", methods={"GET"}, name="api_distribution_contents")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distributionContents(Catalog $catalog, Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionContentApiResource($distribution))->toArray());
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/contents", methods={"POST"}, name="api_distribution_contents_change")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function changeDistributionContents(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasCatalog($catalog) || ! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        try {
            /** @var DistributionContentApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(DistributionContentApiRequest::class, $request);

            $bus->dispatch(new ClearDistributionContentCommand($distribution));

            if ($distribution instanceof CSVDistribution)
            {
                foreach ($parsed as $item) {
                    $bus->dispatch(new AddCSVDistributionContentCommand($distribution, $item->getType(), $item->getValue()));
                }
            }

            return new JsonResponse([], 200);
        } catch (GroupedApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/add", methods={"POST"}, name="api_distribution_add")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     */
    public function addDistribution(Catalog $catalog, Dataset $dataset, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        if (! $dataset->hasCatalog($catalog)) {
            throw $this->createNotFoundException();
        }

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var DistributionApiRequest $parsed */
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            $envelope = $bus->dispatch(
                new AddDistributionCommand(
                    $parsed->getType(),
                    $parsed->getSlug(),
                    $parsed->getTitle(),
                    $parsed->getVersion(),
                    $parsed->getDescription(),
                    $parsed->getLanguage(),
                    $parsed->getLicense(),
                    $parsed->getAccessRights(),
                    $parsed->getIncludeAllData(),
                    $dataset,
                    $user
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), 409);
            }

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/catalog/{catalog}/dataset/{dataset}/distribution/{distribution}/update", methods={"POST"}, name="api_distribution_update")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function updateDistribution(Catalog $catalog, Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        if (! $dataset->hasCatalog($catalog)) {
            throw $this->createNotFoundException();
        }

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var DistributionApiRequest $parsed */
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            $envelope = $bus->dispatch(
                new UpdateDistributionCommand(
                    $distribution,
                    $parsed->getSlug(),
                    $parsed->getTitle(),
                    $parsed->getVersion(),
                    $parsed->getDescription(),
                    $parsed->getLanguage(),
                    $parsed->getLicense(),
                    $parsed->getAccessRights(),
                    $parsed->getIncludeAllData(),
                    $user
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), 409);
            }

            return new JsonResponse([], 500);
        }
    }
}

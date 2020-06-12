<?php
declare(strict_types=1);

namespace App\Api\Controller\Distribution;

use App\Api\Request\Distribution\DistributionApiRequest;
use App\Api\Request\Distribution\DistributionContentApiRequest;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Distribution\DistributionContentApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Exception\GroupedApiRequestParseError;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\AddCSVDistributionContentCommand;
use App\Message\Distribution\AddDistributionCommand;
use App\Message\Distribution\AddDistributionContentsCommand;
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

/**
 * @Route("/api/dataset/{dataset}/distribution")
 * @ParamConverter("dataset", options={"mapping": {"dataset": "slug"}})
 */
class DistributionApiController extends ApiController
{
    /**
     * @Route("/{distribution}", methods={"GET"}, name="api_distribution")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distribution(Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionApiResource($distribution, $this->isGranted('ROLE_ADMIN')))->toArray());
    }

    /**
     * @Route("/{distribution}/contents", methods={"GET"}, name="api_distribution_contents")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distributionContents(Dataset $dataset, Distribution $distribution): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionContentApiResource($distribution))->toArray());
    }

    /**
     * @Route("/{distribution}/contents", methods={"POST"}, name="api_distribution_contents_change")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function changeDistributionContents(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        try {
            /** @var DistributionContentApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(DistributionContentApiRequest::class, $request);

            $bus->dispatch(new ClearDistributionContentCommand($distribution));

            if ($contents instanceof CSVDistribution) {
                foreach ($parsed as $item) {
                    $bus->dispatch(new AddCSVDistributionContentCommand($contents, $item->getType(), $item->getValue()));
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
     * @Route("", methods={"POST"}, name="api_distribution_add")
     */
    public function addDistribution(Dataset $dataset, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        /** @var CastorUser $user */
        $user = $this->getUser();

        try {
            /** @var DistributionApiRequest $parsed */
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            $envelope = $bus->dispatch(
                new AddDistributionCommand(
                    $parsed->getType(),
                    $parsed->getSlug(),
                    $parsed->getLicense(),
                    $dataset,
                    $user
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Distribution $distribution */
            $distribution = $handledStamp->getResult();

            $bus->dispatch(
                new AddDistributionContentsCommand(
                    $distribution,
                    $parsed->getType(),
                    $parsed->getAccessRights(),
                    $parsed->getIncludeAllData(),
                    $user
                )
            );

            if ($parsed->getType() === Distribution::TYPE_RDF) {
                $bus->dispatch(new CreateDistributionDatabaseCommand($distribution));
            }

            return new JsonResponse((new DistributionApiResource($distribution, false))->toArray(), 200);
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
     * @Route("/{distribution}", methods={"POST"}, name="api_distribution_update")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function updateDistribution(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

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

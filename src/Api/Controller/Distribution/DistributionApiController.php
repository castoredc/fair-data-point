<?php
declare(strict_types=1);

namespace App\Api\Controller\Distribution;

use App\Api\Request\Distribution\DistributionApiRequest;
use App\Api\Request\Distribution\DistributionContentApiRequest;
use App\Api\Request\Distribution\DistributionGenerationLogsFilterApiRequest;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Distribution\DistributionContentApiResource;
use App\Api\Resource\Distribution\DistributionGenerationLogApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Exception\GroupedApiRequestParseError;
use App\Exception\LanguageNotFound;
use App\Message\Distribution\AddCSVDistributionContentCommand;
use App\Message\Distribution\ClearDistributionContentCommand;
use App\Message\Distribution\CreateDistributionCommand;
use App\Message\Distribution\CreateDistributionDatabaseCommand;
use App\Message\Distribution\GetDistributionGenerationLogsCommand;
use App\Message\Distribution\UpdateDistributionCommand;
use App\Service\UriHelper;
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
    public function distribution(Dataset $dataset, Distribution $distribution, UriHelper $uriHelper): Response
    {
        $this->denyAccessUnlessGranted('view', $dataset);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionApiResource($distribution, $uriHelper))->toArray());
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
     * @Route("/{distribution}/log", methods={"GET"}, name="api_distribution_logs")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function distributionGenerationLogs(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);
        $contents = $distribution->getContents();

        if (! $dataset->hasDistribution($distribution) || ! $contents instanceof RDFDistribution || ! $contents->isCached()) {
            throw $this->createNotFoundException();
        }

        try {
            /** @var DistributionGenerationLogsFilterApiRequest $parsed */
            $parsed = $this->parseRequest(DistributionGenerationLogsFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new GetDistributionGenerationLogsCommand(
                    $distribution,
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(DistributionGenerationLogApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), 409);
            }
            $this->logger->critical('An error occurred while getting the distribution generation logs', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], 500);
        }
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
            $this->logger->critical('An error occurred while changing the distribution contents', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("", methods={"POST"}, name="api_distribution_add")
     */
    public function addDistribution(Dataset $dataset, Request $request, MessageBusInterface $bus, UriHelper $uriHelper): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        try {
            /** @var DistributionApiRequest $parsed */
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            $envelope = $bus->dispatch(
                new CreateDistributionCommand(
                    $parsed->getType(),
                    $parsed->getSlug(),
                    $parsed->getLicense(),
                    $dataset,
                    $parsed->getAccessRights(),
                    $parsed->getIncludeAllData(),
                    $parsed->getDataModel(),
                    $parsed->getApiUser(),
                    $parsed->getClientId(),
                    $parsed->getClientSecret()
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Distribution $distribution */
            $distribution = $handledStamp->getResult();

            if ($parsed->getType()->isRdf()) {
                $bus->dispatch(new CreateDistributionDatabaseCommand($distribution));
            }

            return new JsonResponse((new DistributionApiResource($distribution, $uriHelper))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), 409);
            }

            $this->logger->critical('An error occurred while adding a distribution', ['exception' => $e]);

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

        try {
            /** @var DistributionApiRequest $parsed */
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            $bus->dispatch(
                new UpdateDistributionCommand(
                    $distribution,
                    $parsed->getSlug(),
                    $parsed->getLicense(),
                    $parsed->getAccessRights(),
                    $parsed->getIncludeAllData(),
                    $parsed->getDataModel(),
                    $parsed->getDataModelVersion(),
                    $parsed->getApiUser(),
                    $parsed->getClientId(),
                    $parsed->getClientSecret(),
                    $parsed->getPublished()
                )
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), 409);
            }

            $this->logger->critical('An error occurred while updating a distribution', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

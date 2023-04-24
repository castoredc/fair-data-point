<?php
declare(strict_types=1);

namespace App\Api\Controller\Distribution;

use App\Api\Controller\ApiController;
use App\Api\Request\Distribution\DistributionApiRequest;
use App\Api\Request\Distribution\DistributionGenerationLogsFilterApiRequest;
use App\Api\Request\Distribution\DistributionSubsetApiRequest;
use App\Api\Resource\Distribution\DistributionApiResource;
use App\Api\Resource\Distribution\DistributionContentApiResource;
use App\Api\Resource\Distribution\DistributionGenerationLogApiResource;
use App\Api\Resource\Distribution\DistributionGenerationRecordLogApiResource;
use App\Command\Distribution\CSV\CreateCSVDistributionCommand;
use App\Command\Distribution\CSV\UpdateCSVDistributionCommand;
use App\Command\Distribution\GetDistributionGenerationLogsCommand;
use App\Command\Distribution\GetDistributionGenerationRecordLogsCommand;
use App\Command\Distribution\RDF\CreateRDFDistributionCommand;
use App\Command\Distribution\RDF\UpdateRDFDistributionCommand;
use App\Command\Distribution\UpdateDistributionSubsetCommand;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Exception\LanguageNotFound;
use App\Security\Authorization\Voter\DistributionVoter;
use App\Service\UriHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

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

        return $this->getResponse(
            new DistributionApiResource($distribution, $uriHelper),
            $distribution,
            [DistributionVoter::VIEW, DistributionVoter::EDIT, DistributionVoter::MANAGE, DistributionVoter::ACCESS_DATA]
        );
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
            $parsed = $this->parseRequest(DistributionGenerationLogsFilterApiRequest::class, $request);
            assert($parsed instanceof DistributionGenerationLogsFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetDistributionGenerationLogsCommand(
                    $distribution,
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(DistributionGenerationLogApiResource::class, $results);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while getting the distribution generation logs', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{distribution}/log/{log}", methods={"GET"}, name="api_distribution_log")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     * @ParamConverter("log", options={"mapping": {"log": "id"}})
     */
    public function distributionGenerationLog(Dataset $dataset, Distribution $distribution, DistributionGenerationLog $log): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasDistribution($distribution) || $log->getDistribution()->getDistribution() !== $distribution) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse((new DistributionGenerationLogApiResource($log))->toArray());
    }

    /**
     * @Route("/{distribution}/log/{log}/records", methods={"GET"}, name="api_distribution_log_records")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     * @ParamConverter("log", options={"mapping": {"log": "id"}})
     */
    public function distributionGenerationLogRecords(Dataset $dataset, Distribution $distribution, DistributionGenerationLog $log, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        if (! $dataset->hasDistribution($distribution) || $log->getDistribution()->getDistribution() !== $distribution) {
            throw $this->createNotFoundException();
        }

        try {
            $parsed = $this->parseRequest(DistributionGenerationLogsFilterApiRequest::class, $request);
            assert($parsed instanceof DistributionGenerationLogsFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetDistributionGenerationRecordLogsCommand(
                    $log,
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(DistributionGenerationRecordLogApiResource::class, $results);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while getting the distribution generation record logs', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
                'LogID' => $log->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("", methods={"POST"}, name="api_distribution_add") */
    public function addDistribution(Dataset $dataset, Request $request, MessageBusInterface $bus, UriHelper $uriHelper): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        try {
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            assert($parsed instanceof DistributionApiRequest);

            if ($parsed->getType()->isCsv()) {
                $envelope = $bus->dispatch(
                    new CreateCSVDistributionCommand(
                        $parsed->getSlug(),
                        $parsed->getLicense(),
                        $dataset,
                        $parsed->getApiUser(),
                        $parsed->getClientId(),
                        $parsed->getClientSecret(),
                        $parsed->getDataDictionary(),
                        $parsed->getDataDictionaryVersion()
                    )
                );
            } elseif ($parsed->getType()->isRdf()) {
                $envelope = $bus->dispatch(
                    new CreateRDFDistributionCommand(
                        $parsed->getSlug(),
                        $parsed->getLicense(),
                        $dataset,
                        $parsed->getApiUser(),
                        $parsed->getClientId(),
                        $parsed->getClientSecret(),
                        $parsed->getDataModel(),
                        $parsed->getDataModelVersion()
                    )
                );
            } else {
                return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $distribution = $handledStamp->getResult();

            return new JsonResponse((new DistributionApiResource($distribution, $uriHelper))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while adding a distribution', [
                'exception' => $e,
                'details' => $e->getMessage(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $parsed = $this->parseRequest(DistributionApiRequest::class, $request);
            assert($parsed instanceof DistributionApiRequest);

            if ($distribution->getContents() instanceof RDFDistribution) {
                $bus->dispatch(
                    new UpdateRDFDistributionCommand(
                        $distribution,
                        $parsed->getSlug(),
                        $parsed->getLicense(),
                        $parsed->getApiUser(),
                        $parsed->getClientId(),
                        $parsed->getClientSecret(),
                        $parsed->getPublished(),
                        $parsed->isCached(),
                        $parsed->isPublic(),
                        $parsed->getDataModel(),
                        $parsed->getDataModelVersion()
                    )
                );
            } elseif ($distribution->getContents() instanceof CSVDistribution) {
                new UpdateCSVDistributionCommand(
                    $distribution,
                    $parsed->getSlug(),
                    $parsed->getLicense(),
                    $parsed->getApiUser(),
                    $parsed->getClientId(),
                    $parsed->getClientSecret(),
                    $parsed->getPublished(),
                    $parsed->isCached(),
                    $parsed->isPublic(),
                    $parsed->getDataModel(),
                    $parsed->getDataModelVersion()
                );
            } else {
                return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof LanguageNotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            $this->logger->critical('An error occurred while updating a distribution', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{distribution}/subset", methods={"POST"}, name="api_distribution_subset")
     * @ParamConverter("distribution", options={"mapping": {"distribution": "slug"}})
     */
    public function subsetDistribution(Dataset $dataset, Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataset);

        try {
            $parsed = $this->parseRequest(DistributionSubsetApiRequest::class, $request);
            assert($parsed instanceof DistributionSubsetApiRequest);
            $bus->dispatch(
                new UpdateDistributionSubsetCommand(
                    $distribution,
                    $parsed->getDependencies()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while updating the subset of a distribution', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

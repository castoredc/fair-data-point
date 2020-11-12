<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\DistributionMetadataApiRequest;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Command\Metadata\CreateDistributionMetadataCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata/distribution/{distribution}")
 * @ParamConverter("distribution", options={"mapping": {"distribution": "id"}})
 */
class DistributionMetadataController extends ApiController
{
    /**
     * @Route("", methods={"POST"}, name="api_metadata_distribution_add")
     */
    public function addDistributionMetadata(Distribution $distribution, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $distribution);

        try {
            $parsed = $this->parseRequest(DistributionMetadataApiRequest::class, $request);
            assert($parsed instanceof DistributionMetadataApiRequest);

            $bus->dispatch(
                new CreateDistributionMetadataCommand(
                    $distribution,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getLanguage(),
                    $parsed->getLicense(),
                    $parsed->getVersionUpdate(),
                    $parsed->getPublishers()
                )
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding metadata for a distribution', [
                'exception' => $e,
                'Distribution' => $distribution->getSlug(),
                'DistributionID' => $distribution->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

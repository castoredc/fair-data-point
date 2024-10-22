<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\CreateMetadataVersionApiRequest;
use App\Command\Metadata\CreateDistributionMetadataCommand;
use App\Entity\FAIRData\Distribution;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DistributionVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata/distribution/{distribution}')]
class DistributionMetadataController extends ApiController
{
    #[Route(path: '', methods: ['POST'], name: 'api_metadata_distribution_add')]
    public function addDistributionMetadata(
        #[MapEntity(mapping: ['distribution' => 'id'])]
        Distribution $distribution,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DistributionVoter::EDIT, $distribution);

        try {
            $parsed = $this->parseRequest(CreateMetadataVersionApiRequest::class, $request);
            assert($parsed instanceof CreateMetadataVersionApiRequest);

            $this->bus->dispatch(
                new CreateDistributionMetadataCommand(
                    $distribution,
                    $parsed->getVersionType(),
                    $parsed->getModel(),
                    $parsed->getModelVersion(),
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while adding metadata for a distribution',
                [
                    'exception' => $e,
                    'Distribution' => $distribution->getSlug(),
                    'DistributionID' => $distribution->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

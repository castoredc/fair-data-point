<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\FAIRDataPointMetadataApiRequest;
use App\Command\Metadata\CreateFAIRDataPointMetadataCommand;
use App\Exception\ApiRequestParseError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata/fdp")
 */
class FAIRDataPointMetadataController extends ApiController
{
    /**
     * @Route("", methods={"POST"}, name="api_metadata_fdp_add")
     */
    public function addCatalogMetadata(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(FAIRDataPointMetadataApiRequest::class, $request);
            assert($parsed instanceof FAIRDataPointMetadataApiRequest);

            $bus->dispatch(
                new CreateFAIRDataPointMetadataCommand(
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
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding metadata for the FAIR Data Point', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

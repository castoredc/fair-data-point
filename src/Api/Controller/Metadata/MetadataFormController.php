<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\MetadataValuesApiRequest;
use App\Api\Resource\Metadata\MetadataFormsApiResource;
use App\Command\Metadata\UpdateMetadataCommand;
use App\Entity\Metadata\Metadata;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\RenderableApiException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata/form/{metadata}")
 * @ParamConverter("metadata", options={"mapping": {"metadata": "id"}})
 */
class MetadataFormController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_metadata_metadata_form_get") */
    public function getMetadataForm(Metadata $metadata, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadata->getEntity());

        return new JsonResponse((new MetadataFormsApiResource($metadata))->toArray());
    }

    /** @Route("", methods={"POST"}, name="api_metadata_metadata_form_update") */
    public function handleMetadataUpdate(Metadata $metadata, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadata->getEntity());

        try {
            $parsed = $this->parseDynamicRequest(MetadataValuesApiRequest::class, $request, $metadata);
            assert($parsed instanceof MetadataValuesApiRequest);

            $bus->dispatch(
                new UpdateMetadataCommand(
                    $metadata,
                    $parsed->getValues(),
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while updating metadata', [
                'exception' => $e,
                'MetadataID' => $metadata->getId(),
            ]);

            if ($e instanceof NotFound) {
                return new JsonResponse([], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof RenderableApiException) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

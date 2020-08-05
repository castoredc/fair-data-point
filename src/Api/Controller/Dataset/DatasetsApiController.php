<?php
declare(strict_types=1);

namespace App\Api\Controller\Dataset;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Exception\ApiRequestParseError;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/dataset")
 */
class DatasetsApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_datasets")
     */
    public function datasets(Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyMetadataFilterApiRequest $parsed */
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new GetPaginatedDatasetsCommand(
                    null,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry(),
                    $parsed->getHideCatalogs(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            $results = $handledStamp->getResult();

            return new JsonResponse((new PaginatedApiResource(DatasetApiResource::class, $results, $this->isGranted('ROLE_ADMIN')))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the datasets', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }
}

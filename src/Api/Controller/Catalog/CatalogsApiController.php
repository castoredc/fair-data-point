<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Request\Catalog\CatalogApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Message\Catalog\CreateCatalogCommand;
use App\Message\Catalog\GetCatalogsCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/catalog")
 */
class CatalogsApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_catalogs")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCatalogsCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_catalog_add")
     */
    public function addCatalog(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            /** @var CatalogApiRequest $parsed */
            $parsed = $this->parseRequest(CatalogApiRequest::class, $request);

            $envelope = $bus->dispatch(
                new CreateCatalogCommand($parsed->getSlug(), $parsed->isAcceptSubmissions(), $parsed->isSubmissionAccessesData())
            );

            /** @var HandledStamp $handledStamp */
            $handledStamp = $envelope->last(HandledStamp::class);

            /** @var Catalog $catalog */
            $catalog = $handledStamp->getResult();

            return new JsonResponse((new CatalogApiResource($catalog))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }
}

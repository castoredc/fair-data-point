<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\CatalogApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Catalog\CatalogsApiResource;
use App\Command\Catalog\CreateCatalogCommand;
use App\Command\Catalog\GetCatalogsCommand;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

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

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);
        $catalogs = $handledStamp->getResult();

        return new JsonResponse((new CatalogsApiResource($catalogs))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_catalog_add")
     */
    public function addCatalog(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(CatalogApiRequest::class, $request);
            assert($parsed instanceof CatalogApiRequest);

            $envelope = $bus->dispatch(
                new CreateCatalogCommand($parsed->getSlug(), $parsed->isAcceptSubmissions(), $parsed->isSubmissionAccessesData())
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $catalog = $handledStamp->getResult();
            assert($catalog instanceof Catalog);

            return new JsonResponse((new CatalogApiResource($catalog))->toArray(), 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a catalog', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }
}

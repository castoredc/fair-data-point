<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\CatalogApiRequest;
use App\Api\Request\Metadata\CatalogMetadataFilterApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Command\Catalog\CreateCatalogCommand;
use App\Command\Catalog\GetPaginatedCatalogsCommand;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/catalog')]
class CatalogsApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_catalogs')]
    public function catalogs(Request $request): Response
    {
        try {
            $parsed = $this->parseRequest(CatalogMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof CatalogMetadataFilterApiRequest);

            $envelope = $this->bus->dispatch(
                new GetPaginatedCatalogsCommand(
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    $parsed->getSearch(),
                    null,
                    null,
                    $parsed->getAcceptSubmissions()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                CatalogApiResource::class,
                $results,
                [CatalogVoter::VIEW, CatalogVoter::ADD, CatalogVoter::EDIT, CatalogVoter::MANAGE]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the catalogs', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/my', methods: ['GET'], name: 'api_my_catalogs')]
    public function myCatalogs(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        try {
            $parsed = $this->parseRequest(CatalogMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof CatalogMetadataFilterApiRequest);

            $envelope = $this->bus->dispatch(
                new GetPaginatedCatalogsCommand(
                    $parsed->getPerPage(),
                    $parsed->getPage(),
                    $parsed->getSearch(),
                    null,
                    $user,
                    $parsed->getAcceptSubmissions()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                CatalogApiResource::class,
                $results,
                [CatalogVoter::VIEW, CatalogVoter::ADD, CatalogVoter::EDIT, CatalogVoter::MANAGE]
            );
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while getting the catalogs', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '', methods: ['POST'], name: 'api_catalog_add')]
    public function addCatalog(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(CatalogApiRequest::class, $request);
            assert($parsed instanceof CatalogApiRequest);

            $envelope = $this->bus->dispatch(
                new CreateCatalogCommand(
                    $parsed->getSlug(),
                    $parsed->isAcceptSubmissions(),
                    $parsed->getDefaultMetadataModel(),
                    $parsed->isSubmissionAccessesData()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $catalog = $handledStamp->getResult();
            assert($catalog instanceof Catalog);

            return new JsonResponse((new CatalogApiResource($catalog))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a catalog', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

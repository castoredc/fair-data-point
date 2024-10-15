<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Controller\ApiController;
use App\Api\Request\Catalog\CatalogApiRequest;
use App\Api\Request\Metadata\MetadataFilterApiRequest;
use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\Study\StudiesMapApiResource;
use App\Command\Catalog\UpdateCatalogCommand;
use App\Command\Dataset\GetPaginatedDatasetsCommand;
use App\Command\Study\FilterStudiesCommand;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/catalog/{catalog}')]
#[ParamConverter('catalog', options: ['mapping' => ['catalog' => 'slug']])]
class SingleCatalogApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_catalog')]
    public function catalog(Catalog $catalog): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        return $this->getResponse(
            new CatalogApiResource($catalog),
            $catalog,
            [CatalogVoter::VIEW, CatalogVoter::ADD, CatalogVoter::EDIT, CatalogVoter::MANAGE]
        );
    }

    #[Route(path: '', methods: ['POST'], name: 'api_catalog_update')]
    public function updateCatalog(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(CatalogVoter::EDIT, $catalog);

        try {
            $parsed = $this->parseRequest(CatalogApiRequest::class, $request, $catalog);
            assert($parsed instanceof CatalogApiRequest);

            $bus->dispatch(
                new UpdateCatalogCommand(
                    $catalog,
                    $parsed->getSlug(),
                    $parsed->isAcceptSubmissions(),
                    $parsed->getDefaultMetadataModel(),
                    $parsed->isSubmissionAccessesData()
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while updating the catalog',
                [
                    'exception' => $e,
                    'Catalog' => $catalog->getSlug(),
                    'CatalogID' => $catalog->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/dataset', name: 'api_catalog_datasets')]
    public function datasets(#[MapEntity(mapping: ['catalog' => 'slug'])]
    Catalog $catalog, Request $request, MessageBusInterface $bus,): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);
        $user = $this->getUser();
        assert($user instanceof User || $user === null);

        try {
            $parsed = $this->parseRequest(MetadataFilterApiRequest::class, $request);
            assert($parsed instanceof MetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new GetPaginatedDatasetsCommand(
                    $catalog,
                    null,
                    $user,
                    $parsed->getSearch(),
                    $parsed->getHideParents(),
                    $parsed->getPerPage(),
                    $parsed->getPage()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $results = $handledStamp->getResult();

            return $this->getPaginatedResponse(
                DatasetApiResource::class,
                $results,
                [DatasetVoter::VIEW, DatasetVoter::EDIT, DatasetVoter::MANAGE]
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the datasets for a catalog',
                [
                    'exception' => $e,
                    'Catalog' => $catalog->getSlug(),
                    'CatalogID' => $catalog->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/map', name: 'api_catalog_datasets_map')]
    public function studiesMap(#[MapEntity(mapping: ['catalog' => 'slug'])]
    Catalog $catalog, Request $request, MessageBusInterface $bus,): Response
    {
        $this->denyAccessUnlessGranted('view', $catalog);

        try {
            $parsed = $this->parseRequest(StudyMetadataFilterApiRequest::class, $request);
            assert($parsed instanceof StudyMetadataFilterApiRequest);

            $envelope = $bus->dispatch(
                new FilterStudiesCommand(
                    $catalog,
                    null,
                    $parsed->getSearch(),
                    $parsed->getStudyType(),
                    $parsed->getMethodType(),
                    $parsed->getCountry()
                )
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new StudiesMapApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while getting the map information for a catalog',
                [
                    'exception' => $e,
                    'Catalog' => $catalog->getSlug(),
                    'CatalogID' => $catalog->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\CreateMetadataVersionApiRequest;
use App\Command\Metadata\CreateCatalogMetadataCommand;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\CatalogVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/metadata/catalog/{catalog}')]
#[ParamConverter('catalog', options: ['mapping' => ['catalog' => 'id']])]
class CatalogMetadataController extends ApiController
{
    #[Route(path: '', methods: ['POST'], name: 'api_metadata_catalog_add')]
    public function addCatalogMetadata(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted(CatalogVoter::EDIT, $catalog);

        try {
            $parsed = $this->parseRequest(CreateMetadataVersionApiRequest::class, $request);
            assert($parsed instanceof CreateMetadataVersionApiRequest);

            $bus->dispatch(
                new CreateCatalogMetadataCommand(
                    $catalog,
                    $parsed->getVersionType(),
                    $parsed->getModel(),
                    $parsed->getModelVersion(),
                )
            );

            return new JsonResponse([]);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding metadata for a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\CatalogMetadataApiRequest;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Command\Metadata\CreateCatalogMetadataCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/metadata/catalog/{catalog}")
 * @ParamConverter("catalog", options={"mapping": {"catalog": "id"}})
 */
class CatalogMetadataController extends ApiController
{
    /**
     * @Route("", methods={"POST"}, name="api_metadata_catalog_add")
     */
    public function addCatalogMetadata(Catalog $catalog, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $catalog);

        try {
            $parsed = $this->parseRequest(CatalogMetadataApiRequest::class, $request);
            assert($parsed instanceof CatalogMetadataApiRequest);

            $bus->dispatch(
                new CreateCatalogMetadataCommand(
                    $catalog,
                    $parsed->getTitle(),
                    $parsed->getDescription(),
                    $parsed->getLanguage(),
                    $parsed->getLicense(),
                    $parsed->getVersionUpdate(),
                    $parsed->getPublishers(),
                    $parsed->getHomepage(),
                    $parsed->getLogo(),
                    $parsed->getThemeTaxonomy()
                )
            );

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while adding metadata for a catalog', [
                'exception' => $e,
                'Catalog' => $catalog->getSlug(),
                'CatalogID' => $catalog->getId(),
            ]);

            return new JsonResponse([], 500);
        }
    }
}

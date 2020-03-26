<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\CastorStudyApiRequest;
use App\Api\Resource\CatalogApiResource;
use App\Api\Resource\CatalogBrandApiResource;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseException;
use App\Exception\CatalogNotFoundException;
use App\Exception\NoPermissionException;
use App\Exception\SessionTimeOutException;
use App\Exception\StudyAlreadyExistsException;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\FindStudiesByUserCommand;
use App\Message\Api\Study\GetCatalogBrandCommand;
use App\Message\Api\Study\GetCatalogCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CatalogApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}", name="api_catalogs")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @param Catalog             $catalog
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function studies(Catalog $catalog, MessageBusInterface $bus): Response
    {
        return new JsonResponse((new CatalogApiResource($catalog))->toArray());
    }

    /**
     * @Route("/api/brand/{catalog}", name="api_catalog_brand")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @param Catalog             $catalog
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function brand(Catalog $catalog, MessageBusInterface $bus): Response
    {
        return new JsonResponse((new CatalogBrandApiResource($catalog))->toArray());
    }
}

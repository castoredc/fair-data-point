<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\Dataset\DatasetApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Catalog\CatalogBrandApiResource;
use App\Api\Resource\Dataset\DatasetsApiResource;
use App\Api\Resource\Dataset\DatasetsFilterApiResource;
use App\Api\Resource\Dataset\DatasetsMapApiResource;
use App\Api\Resource\Security\CastorServersApiResource;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Message\Catalog\GetCatalogsCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Message\Security\GetCastorServersCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class CastorServersApiController extends ApiController
{
    /**
     * @Route("/api/servers", name="api_servers")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCastorServersCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new CastorServersApiResource($handledStamp->getResult()))->toArray());
    }
}

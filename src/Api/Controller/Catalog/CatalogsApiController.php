<?php
declare(strict_types=1);

namespace App\Api\Controller\Catalog;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Request\Study\AddStudyToCatalogApiRequest;
use App\Api\Resource\Catalog\CatalogApiResource;
use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudiesMapApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\FAIRData\Catalog;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use App\Message\Catalog\GetCatalogsCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use App\Message\Study\AddStudyToCatalogCommand;
use App\Message\Study\FilterStudiesCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudiesCommand;
use App\Message\Study\GetStudyCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Route("", name="api_catalogs")
     */
    public function catalogs(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCatalogsCommand());

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }
}

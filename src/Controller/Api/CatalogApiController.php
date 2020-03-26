<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\CastorStudyApiRequest;
use App\Exception\ApiRequestParseException;
use App\Exception\CatalogNotFoundException;
use App\Exception\NoPermissionException;
use App\Exception\SessionTimeOutException;
use App\Exception\StudyAlreadyExistsException;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\FindStudiesByUserCommand;
use App\Message\Api\Study\GetCatalogCommand;
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
     */
    public function studies(string $catalog, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new GetCatalogCommand($catalog));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult()->toArray());
        }
        catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof CatalogNotFoundException) {
                return new JsonResponse($e->toArray(), 404);
            }
        }
    }
}

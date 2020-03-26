<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\CastorStudyApiRequest;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseException;
use App\Exception\CatalogNotFoundException;
use App\Exception\StudyAlreadyExistsException;
use App\Exception\StudyAlreadyHasDatasetException;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\FindStudiesByUserCommand;
use App\Message\Api\Study\PublishStudyInCatalogCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class PublishDatasetApiController extends ApiController
{
    /**
     * @Route("/api/catalog/{catalog}/study/{studyId}/publish", methods={"POST"}, name="api_publish_study_metadata")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function publishStudyMetadata(Catalog $catalog, Study $study, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new PublishStudyInCatalogCommand($study, $catalog));
            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse([]);
        }
        catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof CatalogNotFoundException || $e instanceof StudyNotFoundException) {
                return new JsonResponse($e->toArray(), 404);
            }
            if ($e instanceof StudyAlreadyHasDatasetException) {
                return new JsonResponse($e->toArray(), 400);
            }
        }
    }
}

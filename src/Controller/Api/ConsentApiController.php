<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\ConsentApiRequest;
use App\Api\Resource\ConsentApiResource;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Exception\ApiRequestParseError;
use App\Exception\CatalogNotFound;
use App\Exception\StudyAlreadyHasDataset;
use App\Exception\StudyAlreadyHasSameDataset;
use App\Exception\StudyNotFound;
use App\Message\Api\Study\PublishStudyInCatalogCommand;
use App\Message\Api\Study\UpdateConsentCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ConsentApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/consent", methods={"GET"}, name="api_get_consent")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function getConsent(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            return new JsonResponse((new ConsentApiResource($study->getLatestMetadata()))->toArray());
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/catalog/{catalog}/study/{studyId}/consent", methods={"POST"}, name="api_change_consent")
     * @ParamConverter("catalog", options={"mapping": {"catalog": "slug"}})
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function changeConsent(Catalog $catalog, Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('add', $catalog);
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            /** @var ConsentApiRequest $parsed */
            $parsed = $this->parseRequest(ConsentApiRequest::class, $request);

            $bus->dispatch(
                new UpdateConsentCommand(
                    $study->getLatestMetadata(),
                    $parsed->getPublish(),
                    $parsed->getSocialMedia()
                )
            );

            if ($parsed->getPublish()) {
                $bus->dispatch(new PublishStudyInCatalogCommand($study, $catalog));
            }

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof CatalogNotFound || $e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), 404);
            }
            if ($e instanceof StudyAlreadyHasDataset) {
                return new JsonResponse($e->toArray(), 400);
            }
            if ($e instanceof StudyAlreadyHasSameDataset) {
                return new JsonResponse([], 200);
            }

            return new JsonResponse([], 500);
        }
    }
}

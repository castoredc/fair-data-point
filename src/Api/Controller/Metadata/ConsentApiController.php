<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Request\Metadata\ConsentApiRequest;
use App\Api\Resource\Metadata\ConsentApiResource;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Exception\StudyAlreadyHasDataset;
use App\Exception\StudyAlreadyHasSameDataset;
use App\Exception\StudyNotFound;
use App\Message\Metadata\UpdateConsentCommand;
use App\Message\Study\PublishStudyCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/study/{studyId}/consent")
 * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
 */
class ConsentApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_get_consent")
     */
    public function getConsent(Study $study): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        return new JsonResponse((new ConsentApiResource($study->getLatestMetadata()))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_change_consent")
     */
    public function changeConsent(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            $parsed = $this->parseRequest(ConsentApiRequest::class, $request);
            assert($parsed instanceof ConsentApiRequest);

            $bus->dispatch(
                new UpdateConsentCommand(
                    $study,
                    $parsed->getPublish(),
                    $parsed->getSocialMedia()
                )
            );

            if ($parsed->getPublish()) {
                $bus->dispatch(new PublishStudyCommand($study));
            }

            return new JsonResponse([], 200);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof StudyNotFound) {
                return new JsonResponse($e->toArray(), 404);
            }

            if ($e instanceof StudyAlreadyHasDataset) {
                return new JsonResponse($e->toArray(), 400);
            }

            if ($e instanceof StudyAlreadyHasSameDataset) {
                return new JsonResponse([], 200);
            }

            $this->logger->critical('An error occurred while updating consent information', [
                'exception' => $e,
                'Study' => $study->getSlug(),
                'StudyID' => $study->getId(),
            ]);

            return new JsonResponse([$e->getMessage()], 500);
        }
    }
}

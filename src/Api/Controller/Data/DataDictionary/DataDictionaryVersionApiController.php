<?php
declare(strict_types=1);

namespace App\Api\Controller\Data\DataDictionary;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataDictionary\DataDictionaryVersionTypeApiRequest;
use App\Api\Resource\Data\DataDictionary\DataDictionaryVersionApiResource;
use App\Api\Resource\Data\DataDictionary\DataDictionaryVersionExportApiResource;
use App\Command\Data\DataDictionary\CreateDataDictionaryVersionCommand;
use App\Entity\Data\DataDictionary\DataDictionary;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use App\Exception\ApiRequestParseError;
use Cocur\Slugify\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function sprintf;
use const JSON_PRETTY_PRINT;

/**
 * @Route("/api/dictionary/{dataDictionary}/v/{version}")
 * @ParamConverter("dataDictionaryVersion", options={"mapping": {"dataDictionary": "data_dictionary", "version": "id"}})
 */
class DataDictionaryVersionApiController extends ApiController
{
    /**
     * @Route("", methods={"GET"}, name="api_dictionary_version")
     */
    public function dataDictionaryVersion(DataDictionaryVersion $dataDictionaryVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataDictionaryVersion->getDataDictionary());

        return new JsonResponse((new DataDictionaryVersionApiResource($dataDictionaryVersion))->toArray());
    }

    /**
     * @Route("", methods={"POST"}, name="api_dictionary_version_create")
     */
    public function createDataDictionaryVersion(DataDictionary $dataDictionary, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataDictionary);

        try {
            $parsed = $this->parseRequest(DataDictionaryVersionTypeApiRequest::class, $request);
            assert($parsed instanceof DataDictionaryVersionTypeApiRequest);

            $envelope = $bus->dispatch(new CreateDataDictionaryVersionCommand($dataDictionary, $parsed->getVersionType()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataDictionaryVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while creating a data dictionary version', [
                'exception' => $e,
                'dataDictionary' => $dataDictionary->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/export", methods={"GET"}, name="api_dictionary_version_export")
     */
    public function exportDataDictionaryVersion(DataDictionaryVersion $dataDictionaryVersion, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataDictionaryVersion->getDataDictionary());

        $response = new JsonResponse((new DataDictionaryVersionExportApiResource($dataDictionaryVersion))->toArray());

        $slugify = new Slugify();
        $name = sprintf('%s - %s.json', $slugify->slugify($dataDictionaryVersion->getDataDictionary()->getTitle()), $dataDictionaryVersion->getVersion()->getValue());
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}

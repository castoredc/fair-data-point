<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataDictionary;

use App\Api\Controller\ApiController;
use App\Api\Request\DataSpecification\DataDictionary\DataDictionaryVersionTypeApiRequest;
use App\Api\Resource\DataSpecification\DataDictionary\DataDictionaryVersionApiResource;
use App\Api\Resource\DataSpecification\DataDictionary\DataDictionaryVersionExportApiResource;
use App\Command\DataSpecification\DataDictionary\CreateDataDictionaryVersionCommand;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Exception\ApiRequestParseError;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function sprintf;
use const JSON_PRETTY_PRINT;

#[Route(path: '/api/dictionary/{dataDictionary}/v/{version}')]
class DataDictionaryVersionApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_dictionary_version')]
    public function dataDictionaryVersion(
        #[MapEntity(mapping: ['dataDictionary' => 'data_dictionary', 'version' => 'id'])]
        DataDictionaryVersion $dataDictionaryVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataDictionaryVersion->getDataDictionary());

        return new JsonResponse((new DataDictionaryVersionApiResource($dataDictionaryVersion))->toArray());
    }

    #[Route(path: '', methods: ['POST'], name: 'api_dictionary_version_create')]
    public function createDataDictionaryVersion(
        DataDictionary $dataDictionary,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataDictionary);

        try {
            $parsed = $this->parseRequest(DataDictionaryVersionTypeApiRequest::class, $request);
            assert($parsed instanceof DataDictionaryVersionTypeApiRequest);

            $envelope = $this->bus->dispatch(
                new CreateDataDictionaryVersionCommand($dataDictionary, $parsed->getVersionType())
            );

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataDictionaryVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical(
                'An error occurred while creating a data dictionary version',
                [
                    'exception' => $e,
                    'dataDictionary' => $dataDictionary->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/export', methods: ['GET'], name: 'api_dictionary_version_export')]
    public function exportDataDictionaryVersion(
        #[MapEntity(mapping: ['dataDictionary' => 'data_dictionary', 'version' => 'id'])]
        DataDictionaryVersion $dataDictionaryVersion,
    ): Response {
        $this->denyAccessUnlessGranted(DataSpecificationVoter::EDIT, $dataDictionaryVersion->getDataDictionary());

        $response = new JsonResponse((new DataDictionaryVersionExportApiResource($dataDictionaryVersion))->toArray());

        $slugify = new Slugify();
        $name = sprintf(
            '%s - %s.json',
            $slugify->slugify($dataDictionaryVersion->getDataDictionary()->getTitle()),
            $dataDictionaryVersion->getVersion()->getValue()
        );
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}

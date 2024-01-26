<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataDictionary;

use App\Api\Controller\ApiController;
use App\Api\Request\Data\DataDictionary\DataDictionaryVersionApiRequest;
use App\Api\Resource\Data\DataDictionary\DataDictionaryApiResource;
use App\Api\Resource\Data\DataDictionary\DataDictionaryVersionApiResource;
use App\Command\DataSpecification\DataDictionary\ImportDataDictionaryCommand;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Exception\ApiRequestParseError;
use App\Exception\InvalidDataDictionaryVersion;
use App\Exception\Upload\EmptyFile;
use App\Exception\Upload\InvalidFile;
use App\Exception\Upload\InvalidJSON;
use App\Exception\Upload\NoFileSpecified;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/dictionary/{dataDictionary}")
 * @ParamConverter("dataDictionary", options={"mapping": {"dataDictionary": "id"}})
 */
class DataDictionaryApiController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_dictionary") */
    public function dataDictionary(DataDictionary $dataDictionary): Response
    {
        $this->denyAccessUnlessGranted('view', $dataDictionary);

        return new JsonResponse((new DataDictionaryApiResource($dataDictionary))->toArray());
    }

    /** @Route("/import", methods={"POST"}, name="api_dictionary_import") */
    public function importDataDictionaryVersion(DataDictionary $dataDictionary, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $dataDictionary);

        $file = $request->files->get('file');
        assert($file instanceof UploadedFile || $file === null);

        try {
            if ($file === null) {
                throw new NoFileSpecified();
            }

            $parsed = $this->parseRequest(DataDictionaryVersionApiRequest::class, $request);
            assert($parsed instanceof DataDictionaryVersionApiRequest);

            $envelope = $bus->dispatch(new ImportDataDictionaryCommand($dataDictionary, $file, $parsed->getVersion()));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse((new DataDictionaryVersionApiResource($handledStamp->getResult()))->toArray());
        } catch (NoFileSpecified $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof InvalidFile || $e instanceof EmptyFile || $e instanceof InvalidJSON || $e instanceof InvalidDataDictionaryVersion) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical('An error occurred while importing a data dictionary', [
                'exception' => $e,
                'dataDictionary' => $dataDictionary->getId(),
            ]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Controller\Tools;

use App\Api\Controller\ApiController;
use App\Exception\NoFieldsFound;
use App\Exception\NoMetadataTypesFound;
use App\Command\Tools\MetadataXmlParseCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;
use function file_get_contents;

class ToolsApiController extends ApiController
{
    /**
     * @Route("/api/tools/metadata-xml-parse", name="api_tools_metadata_xml_to_csv")
     */
    public function metadataXmlParse(Request $request, MessageBusInterface $bus): Response
    {
        $file = $request->files->get('xml');
        assert($file instanceof UploadedFile || $file === null);

        if ($file === null) {
            return new JsonResponse(['error' => 'No file specified'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $file->isValid()) {
            return new JsonResponse([
                'error' => $file->getErrorMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $xml = file_get_contents($file->getPathname());

        if ($xml === false) {
            return new JsonResponse(['error' => 'The uploaded file is empty.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $envelope = $bus->dispatch(new MetadataXmlParseCommand($xml));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse($handledStamp->getResult());
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NoMetadataTypesFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($e instanceof NoFieldsFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return new JsonResponse([], 500);
        }
    }
}

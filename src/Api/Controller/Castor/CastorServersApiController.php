<?php
declare(strict_types=1);

namespace App\Api\Controller\Castor;

use App\Api\Controller\ApiController;
use App\Api\Request\Security\CastorServerApiRequest;
use App\Api\Resource\Security\CastorServerApiResource;
use App\Api\Resource\Security\CastorServersApiResource;
use App\Command\Castor\DeleteCastorServerCommand;
use App\Command\Security\GetCastorServersCommand;
use App\Exception\ApiRequestParseError;
use App\Exception\Castor\CastorServerNotFound;
use App\Model\Castor\ApiClient;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function assert;

final class CastorServersApiController extends ApiController
{
    private EncryptionService $encryptionService;

    public function __construct(
        ApiClient $apiClient,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        EncryptionService $encryptionService
    ) {
        parent::__construct($apiClient, $validator, $logger, $em);
        $this->encryptionService = $encryptionService;
    }

    /** @Route("/api/castor/servers", methods={"GET"}, name="api_servers") */
    public function servers(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetCastorServersCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse(
            (new CastorServersApiResource(
                $handledStamp->getResult(),
                $this->isGranted('ROLE_ADMIN'),
                $this->encryptionService
            ))->toArray()
        );
    }

    /** @Route("/api/castor/servers", methods={"POST", "PUT"}, name="api_add_server") */
    public function addServer(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $parsed = $this->parseRequest(CastorServerApiRequest::class, $request);
            assert($parsed instanceof CastorServerApiRequest);

            $envelope = $bus->dispatch($parsed->toCommand());
            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            return new JsonResponse(
                (new CastorServerApiResource($handledStamp->getResult(), true, $this->encryptionService))->toArray()
            );
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating a CastorServer', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/api/castor/servers/{id}", methods={"DELETE"}, name="delete_add_server") */
    public function deleteServer(int $id, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $bus->dispatch(new DeleteCastorServerCommand($id));

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (CastorServerNotFound $e) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() instanceof CastorServerNotFound) {
                $this->logger->warning('The CastorServer with id ' . $id . ' could not be found.', ['exception' => $e->getPrevious()]);

                return new JsonResponse([], Response::HTTP_NOT_FOUND);
            }

            $this->logger->critical('An error occurred while deleting a CastorServer', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

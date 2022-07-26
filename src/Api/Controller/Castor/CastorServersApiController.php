<?php
declare(strict_types=1);

namespace App\Api\Controller\Castor;

use App\Api\Controller\ApiController;
use App\Api\Resource\Security\CastorServersApiResource;
use App\Command\Security\GetCastorServersCommand;
use App\Model\Castor\ApiClient;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function assert;

class CastorServersApiController extends ApiController
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

    /** @Route("/api/castor/servers", name="api_servers") */
    public function catalogs(MessageBusInterface $bus): Response
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
}

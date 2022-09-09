<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\GetRDFFromStoreCommand;
use App\Exception\NoAccessPermission;
use App\Service\EncryptionService;
use App\Service\TripleStoreBasedDistributionService;
use App\Service\UriHelper;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetRDFFromStoreCommandHandler implements MessageHandlerInterface
{
    private TripleStoreBasedDistributionService $tripleStoreBasedDistributionService;

    private UriHelper $uriHelper;

    private EncryptionService $encryptionService;

    private Security $security;

    public function __construct(TripleStoreBasedDistributionService $tripleStoreBasedDistributionService, UriHelper $uriHelper, EncryptionService $encryptionService, Security $security)
    {
        $this->tripleStoreBasedDistributionService = $tripleStoreBasedDistributionService;
        $this->uriHelper = $uriHelper;
        $this->encryptionService = $encryptionService;
        $this->security = $security;
    }

    /** @throws Exception */
    public function __invoke(GetRDFFromStoreCommand $command): string
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $this->tripleStoreBasedDistributionService->createDistributionConnection($distribution->getDatabaseInformation(), $this->encryptionService);

        if ($command->getRecord() !== null) {
            return $this->tripleStoreBasedDistributionService->getDataFromStore($this->uriHelper->getUri($command->getDistribution()) . '/g/' . $command->getRecord());
        }

        return $this->tripleStoreBasedDistributionService->getDataFromStore();
    }
}

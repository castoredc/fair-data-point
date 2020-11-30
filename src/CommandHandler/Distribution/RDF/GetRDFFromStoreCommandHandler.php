<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\GetRDFFromStoreCommand;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Service\DistributionService;
use App\Service\EncryptionService;
use App\Service\UriHelper;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function array_key_exists;
use function count;
use function is_array;
use function sprintf;

class GetRDFFromStoreCommandHandler implements MessageHandlerInterface
{
    private DistributionService $distributionService;

    private UriHelper $uriHelper;

    private EncryptionService $encryptionService;

    private Security $security;

    public function __construct(DistributionService $distributionService, UriHelper $uriHelper, EncryptionService $encryptionService, Security $security)
    {
        $this->distributionService = $distributionService;
        $this->uriHelper = $uriHelper;
        $this->encryptionService = $encryptionService;
        $this->security = $security;
    }

    /**
     * @throws Exception
     */
    public function __invoke(GetRDFFromStoreCommand $command): string
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDistribution()->getCurrentDataModelVersion();
        $prefixes = $dataModel->getPrefixes();
        $nameSpaces = [];

        foreach ($prefixes as $prefix) {
            /** @var NamespacePrefix $prefix */
            $nameSpaces[$prefix->getPrefix()] = $prefix->getUri()->getValue();
        }

        $store = $this->distributionService->getArc2Store(DistributionService::CURRENT_STORE, $distribution->getDatabaseInformation(), $this->encryptionService);

        if ($command->getRecord() !== null) {
            $url = $this->uriHelper->getUri($command->getDistribution()) . '/g/' . $command->getRecord();
            $result = $store->query(sprintf('SELECT ?s ?p ?o WHERE { GRAPH ?g { ?s ?p ?o . FILTER (?g = <%s>)} }', $url));
        } else {
            $result = $store->query('SELECT * WHERE { ?s ?p ?o . }');
        }

        if (! is_array($result) || ! array_key_exists('result', $result) || count($result['result']['rows']) === 0) {
            throw new NotFound();
        }

        return $store->toTurtle($result['result']['rows'], $nameSpaces);
    }
}

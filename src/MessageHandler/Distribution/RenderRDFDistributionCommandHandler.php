<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Encryption\EncryptionService;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use App\Service\CastorEntityHelper;
use App\Service\RDFRenderHelper;
use App\Service\UriHelper;
use EasyRdf_Graph;
use EasyRdf_Namespace;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use Throwable;
use function assert;

class RenderRDFDistributionCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    /** @var Security */
    private $security;

    /** @var CastorEntityHelper */
    private $entityHelper;

    /** @var UriHelper */
    private $uriHelper;

    /** @var EncryptionService */
    private $encryptionService;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ApiClient $apiClient,
        Security $security,
        CastorEntityHelper $entityHelper,
        UriHelper $uriHelper,
        EncryptionService $encryptionService,
        LoggerInterface $logger
    ) {
        $this->apiClient = $apiClient;
        $this->security = $security;
        $this->entityHelper = $entityHelper;
        $this->uriHelper = $uriHelper;
        $this->encryptionService = $encryptionService;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    public function __invoke(RenderRDFDistributionCommand $command): EasyRdf_Graph
    {
        $contents = $command->getDistribution();
        $distribution = $contents->getDistribution();

        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $apiUser = $distribution->getApiUser();

        if ($apiUser !== null) {
            $this->apiClient->useApiUser($apiUser, $this->encryptionService);
            $this->entityHelper->useApiUser($apiUser);
        } else {
            $this->apiClient->setUser($user);
        }

        $helper = new RDFRenderHelper($distribution, $this->apiClient, $this->entityHelper, $this->uriHelper);

        $graph = new EasyRdf_Graph();

        $dataModel = $contents->getCurrentDataModelVersion();
        $prefixes = $dataModel->getPrefixes();

        foreach ($prefixes as $prefix) {
            /** @var NamespacePrefix $prefix */
            EasyRdf_Namespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        foreach ($command->getRecords() as $record) {
            try {
                $graph = $helper->renderRecord($record, $graph);
            } catch (Throwable $t) {
                $this->logger->critical('An error occurred while rendering the record', [
                    'exception' => $t,
                    'Message' => $t->getMessage(),
                    'Distribution' => $distribution->getSlug(),
                    'DistributionID' => $distribution->getId(),
                    'RecordID' => $record->getId(),
                ]);
            }
        }

        return $graph;
    }
}

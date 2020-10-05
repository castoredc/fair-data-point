<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Encryption\EncryptionService;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotACastorUser;
use App\Message\Distribution\RenderRDFDistributionCommand;
use App\Model\Castor\ApiClient;
use App\Security\User;
use App\Service\CastorEntityHelper;
use App\Service\RDFRenderHelper;
use App\Service\UriHelper;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use Throwable;
use function assert;

class RenderRDFDistributionCommandHandler implements MessageHandlerInterface
{
    private ApiClient $apiClient;

    private Security $security;

    private CastorEntityHelper $entityHelper;

    private UriHelper $uriHelper;

    private EncryptionService $encryptionService;

    private LoggerInterface $logger;

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
    public function __invoke(RenderRDFDistributionCommand $command): Graph
    {
        $contents = $command->getDistribution();
        $distribution = $contents->getDistribution();

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $apiUser = $distribution->getApiUser();

        if ($apiUser !== null) {
            $this->apiClient->useApiUser($apiUser, $this->encryptionService);
            $this->entityHelper->useApiUser($apiUser);
        } else {
            if (! $user->hasCastorUser()) {
                throw new UserNotACastorUser();
            }

            $this->apiClient->setUser($user->getCastorUser());
            $this->entityHelper->useUser($user->getCastorUser());
        }

        $helper = new RDFRenderHelper($distribution, $this->apiClient, $this->entityHelper, $this->uriHelper);

        $graph = new Graph();

        $dataModel = $contents->getCurrentDataModelVersion();
        $prefixes = $dataModel->getPrefixes();

        foreach ($prefixes as $prefix) {
            RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
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

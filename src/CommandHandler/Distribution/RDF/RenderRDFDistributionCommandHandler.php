<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\RenderRDFDistributionCommand;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Security\User;
use App\Service\CastorEntityHelper;
use App\Service\DataTransformationService;
use App\Service\EncryptionService;
use App\Service\RDFRenderHelper;
use App\Service\UriHelper;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;
use function assert;

#[AsMessageHandler]
class RenderRDFDistributionCommandHandler
{
    public function __construct(
        private ApiClient $apiClient,
        private Security $security,
        private CastorEntityHelper $entityHelper,
        private UriHelper $uriHelper,
        private EncryptionService $encryptionService,
        private LoggerInterface $logger,
        private DataTransformationService $dataTransformationService,
    ) {
    }

    /** @throws Exception */
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

        $helper = new RDFRenderHelper($distribution, $this->apiClient, $this->entityHelper, $this->uriHelper, $this->dataTransformationService, null, null);

        $graph = new Graph();

        $dataModel = $contents->getCurrentDataModelVersion();
        $prefixes = $dataModel->getPrefixes();

        foreach ($prefixes as $prefix) {
            RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        $records = $helper->getSubset($command->getRecords());

        foreach ($records as $record) {
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

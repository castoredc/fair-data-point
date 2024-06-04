<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\RenderRDFMetadataCommand;
use App\Exception\NoAccessPermission;
use App\Model\Castor\ApiClient;
use App\Service\DataTransformationService;
use App\Service\EncryptionService;
use App\Service\RDF\RenderRdfMetadataHelper;
use App\Service\UriHelper;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RenderRDFMetadataCommandHandler
{
    public function __construct(
        private ApiClient $apiClient,
        private Security $security,
        private UriHelper $uriHelper,
        private EncryptionService $encryptionService,
        private LoggerInterface $logger,
        private DataTransformationService $dataTransformationService,
    ) {
    }

    /** @throws Exception */
    public function __invoke(RenderRDFMetadataCommand $command): Graph
    {
        $entity = $command->getEntity();

        if (! $this->security->isGranted('view', $entity)) {
            throw new NoAccessPermission();
        }

        $helper = new RenderRdfMetadataHelper($this->uriHelper, $this->security);

        $graph = new Graph();

        $metadata = $entity->getLatestMetadata();

        if($metadata === null) {
            return $graph;
        }

        $metadataModel = $metadata->getMetadataModelVersion();
        $prefixes = $metadataModel->getPrefixes();

        foreach ($prefixes as $prefix) {
            RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
        }

        return $helper->renderEntity($entity, $graph);
    }
}

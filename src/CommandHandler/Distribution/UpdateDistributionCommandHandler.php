<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\UpdateDistributionCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Exception\LanguageNotFound;
use App\Exception\NoAccessPermission;
use App\Security\ApiUser;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
abstract class UpdateDistributionCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security, protected EncryptionService $encryptionService)
    {
    }

    /** @throws LanguageNotFound */
    protected function handleDistributionUpdate(UpdateDistributionCommand $command): Distribution
    {
        $distribution = $command->getDistribution();
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $defaultMetadataModel = $this->em->getRepository(MetadataModel::class)->find($command->getDefaultMetadataModelId());
        assert($defaultMetadataModel instanceof MetadataModel);

        if ($command->getApiUser() !== null && $command->getClientId() !== null && $command->getClientSecret() !== null) {
            $apiUser = new ApiUser($command->getApiUser(), $study->getServer());
            $apiUser->setDecryptedClientId($this->encryptionService, $command->getClientId()->exposeAsString());
            $apiUser->setDecryptedClientSecret($this->encryptionService, $command->getClientSecret()->exposeAsString());

            $this->em->persist($apiUser);

            $distribution->setApiUser($apiUser);
        }

        $license = $this->em->getRepository(License::class)->find($command->getLicense());

        $slug = $command->getSlug();

        $distribution->setSlug($slug);
        $distribution->setLicense($license);
        $distribution->setIsPublished($command->isPublished());
        $distribution->setDefaultMetadataModel($defaultMetadataModel);

        $contents = $distribution->getContents();
        $contents->setIsCached($command->isCached());
        $contents->setIsPublic($command->isPublic());

        return $distribution;
    }
}

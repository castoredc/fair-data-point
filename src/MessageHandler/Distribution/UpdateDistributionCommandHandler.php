<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\License;
use App\Exception\InvalidDataModelVersion;
use App\Exception\LanguageNotFound;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\UpdateDistributionCommand;
use App\Security\ApiUser;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class UpdateDistributionCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    private EncryptionService $encryptionService;

    public function __construct(EntityManagerInterface $em, Security $security, EncryptionService $encryptionService)
    {
        $this->em = $em;
        $this->security = $security;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @throws LanguageNotFound
     */
    public function __invoke(UpdateDistributionCommand $command): void
    {
        $distribution = $command->getDistribution();
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        if ($command->getApiUser() !== null && $command->getClientId() !== null && $command->getClientSecret() !== null) {
            $apiUser = new ApiUser($command->getApiUser(), $study->getServer());
            $apiUser->setDecryptedClientId($this->encryptionService, $command->getClientId()->exposeAsString());
            $apiUser->setDecryptedClientSecret($this->encryptionService, $command->getClientSecret()->exposeAsString());

            $this->em->persist($apiUser);

            $distribution->setApiUser($apiUser);
        }

        $license = $this->em->getRepository(License::class)->find($command->getLicense());

        $distribution->setSlug($command->getSlug());
        $distribution->setLicense($license);

        $contents = $distribution->getContents();
        $contents->setAccessRights($command->getAccessRights());
        $contents->setIsPublished($command->isPublished());

        if ($contents instanceof CSVDistribution) {
            $contents->setIncludeAll($command->getIncludeAllData());
        } elseif ($contents instanceof RDFDistribution) {
            $dataModel = $this->em->getRepository(DataModel::class)->find($command->getDataModel());

            $dataModelVersion = $this->em->getRepository(DataModelVersion::class)->find($command->getDataModelVersion());

            if ($dataModel === null || $dataModelVersion === null || $dataModelVersion->getDataModel() !== $dataModel) {
                throw new InvalidDataModelVersion();
            }

            if ($contents->getDataModel() !== $dataModel) {
                // Switched data model, remove mappings
                foreach ($study->getMappings() as $mapping) {
                    $this->em->remove($mapping);
                }

                $study->getMappings()->clear();
            }

            $contents->setDataModel($dataModel);
            $contents->setCurrentDataModelVersion($dataModelVersion);
        }

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->persist($study);
        $this->em->flush();
    }
}

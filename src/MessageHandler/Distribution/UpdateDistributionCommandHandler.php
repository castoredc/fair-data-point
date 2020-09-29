<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Encryption\EncryptionService;
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
    public function __invoke(UpdateDistributionCommand $message): void
    {
        $distribution = $message->getDistribution();
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        if ($message->getApiUser() !== null && $message->getClientId() !== null && $message->getClientSecret() !== null) {
            $apiUser = new ApiUser($message->getApiUser(), $study->getServer());
            $apiUser->setDecryptedClientId($this->encryptionService, $message->getClientId()->exposeAsString());
            $apiUser->setDecryptedClientSecret($this->encryptionService, $message->getClientSecret()->exposeAsString());

            $this->em->persist($apiUser);

            $distribution->setApiUser($apiUser);
        }

        $license = $this->em->getRepository(License::class)->find($message->getLicense());
        assert($license instanceof License || $license === null);

        $distribution->setSlug($message->getSlug());
        $distribution->setLicense($license);

        $contents = $distribution->getContents();
        $contents->setAccessRights($message->getAccessRights());
        $contents->setIsPublished($message->isPublished());

        if ($contents instanceof CSVDistribution) {
            $contents->setIncludeAll($message->getIncludeAllData());
        } elseif ($contents instanceof RDFDistribution) {
            $dataModel = $this->em->getRepository(DataModel::class)->find($message->getDataModel());
            assert($dataModel instanceof DataModel || $dataModel === null);

            $dataModelVersion = $this->em->getRepository(DataModelVersion::class)->find($message->getDataModelVersion());
            assert($dataModelVersion instanceof DataModelVersion || $dataModelVersion === null);

            if ($dataModel === null || $dataModelVersion === null || $dataModelVersion->getDataModel() !== $dataModel) {
                throw new InvalidDataModelVersion();
            }

            if ($contents->getDataModel() !== $dataModel) {
                // Switched data model, remove mappings
                foreach ($contents->getMappings() as $mapping) {
                    $this->em->remove($mapping);
                }

                $contents->getMappings()->clear();
            }

            $contents->setDataModel($dataModel);
            $contents->setCurrentDataModelVersion($dataModelVersion);
        }

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->flush();
    }
}

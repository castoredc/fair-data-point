<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Exception\InvalidDistributionType;
use App\Exception\NoAccessPermission;
use App\Security\ApiUser;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateDistributionCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateDistributionCommand $command): Distribution
    {
        $dataset = $command->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $dataset)) {
            throw new NoAccessPermission();
        }

        $distribution = new Distribution(
            $command->getSlug(),
            $dataset
        );

        $license = $this->em->getRepository(License::class)->find($command->getLicense());
        $distribution->setLicense($license);

        if ($command->getApiUser() !== null && $command->getClientId() !== null && $command->getClientSecret() !== null) {
            $apiUser = new ApiUser($command->getApiUser(), $study->getServer());
            $apiUser->setDecryptedClientId($this->encryptionService, $command->getClientId()->exposeAsString());
            $apiUser->setDecryptedClientSecret($this->encryptionService, $command->getClientSecret()->exposeAsString());

            $this->em->persist($apiUser);

            $distribution->setApiUser($apiUser);
        }

        if ($command->getType()->isRdf()) {
            $dataModel = $this->em->getRepository(DataModel::class)->find($command->getDataModel());

            $contents = new RDFDistribution(
                $distribution,
                $command->getAccessRights(),
                false
            );

            $contents->setDataModel($dataModel);
            $contents->setCurrentDataModelVersion($dataModel->getLatestVersion());
        } elseif ($command->getType()->isCsv()) {
            $contents = new CSVDistribution(
                $distribution,
                $command->getAccessRights(),
                false,
                $command->getIncludeAllData()
            );
        } else {
            throw new InvalidDistributionType();
        }

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->flush();

        return $distribution;
    }
}

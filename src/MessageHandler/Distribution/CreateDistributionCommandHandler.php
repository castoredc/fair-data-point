<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Encryption\EncryptionService;
use App\Entity\Castor\CastorStudy;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Exception\InvalidDistributionType;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\CreateDistributionCommand;
use App\Security\ApiUser;
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

    public function __invoke(CreateDistributionCommand $message): Distribution
    {
        $dataset = $message->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $dataset)) {
            throw new NoAccessPermission();
        }

        $distribution = new Distribution(
            $message->getSlug(),
            $dataset
        );

        $license = $this->em->getRepository(License::class)->find($message->getLicense());
        assert($license instanceof License || $license === null);
        $distribution->setLicense($license);

        if ($message->getApiUser() !== null && $message->getClientId() !== null && $message->getClientSecret() !== null) {
            $apiUser = new ApiUser($message->getApiUser(), $study->getServer());
            $apiUser->setDecryptedClientId($this->encryptionService, $message->getClientId()->exposeAsString());
            $apiUser->setDecryptedClientSecret($this->encryptionService, $message->getClientSecret()->exposeAsString());

            $this->em->persist($apiUser);

            $distribution->setApiUser($apiUser);
        }

        if ($message->getType()->isRdf()) {
            $dataModel = $this->em->getRepository(DataModel::class)->find($message->getDataModel());
            assert($dataModel instanceof DataModel || $dataModel === null);

            $contents = new RDFDistribution(
                $distribution,
                $message->getAccessRights(),
                false
            );

            $contents->setDataModel($dataModel);
            $contents->setCurrentDataModelVersion($dataModel->getLatestVersion());
        } elseif ($message->getType()->isCsv()) {
            $contents = new CSVDistribution(
                $distribution,
                $message->getAccessRights(),
                false,
                $message->getIncludeAllData()
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

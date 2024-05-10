<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Exception\NoAccessPermission;
use App\Security\ApiUser;
use App\Security\User;
use App\Service\Distribution\MysqlBasedDistributionService;
use App\Service\Distribution\TripleStoreBasedDistributionService;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
abstract class CreateDistributionCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected MysqlBasedDistributionService $mysqlBasedDistributionService, protected TripleStoreBasedDistributionService $tripleStoreBasedDistributionService, protected Security $security, protected EncryptionService $encryptionService)
    {
    }

    protected function handleDistributionCreation(CreateDistributionCommand $command): Distribution
    {
        $dataset = $command->getDataset();
        $user = $this->security->getUser();
        $study = $dataset->getStudy();
        assert($user instanceof User);
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $dataset)) {
            throw new NoAccessPermission();
        }

        $slug = $command->getSlug();

        $distribution = new Distribution(
            $slug,
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

        $distribution->addPermissionForUser($user, PermissionType::manage());

        return $distribution;
    }
}

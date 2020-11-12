<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Exception\CouldNotConnectToMySqlServer;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Exception\NoAccessPermission;
use App\Command\Distribution\CreateDistributionDatabaseCommand;
use App\Service\DistributionService;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function bin2hex;
use function random_bytes;

class CreateDistributionDatabaseCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private DistributionService $distributionService;

    private EncryptionService $encryptionService;

    private Security $security;

    public function __construct(EntityManagerInterface $em, DistributionService $distributionService, EncryptionService $encryptionService, Security $security)
    {
        $this->em = $em;
        $this->distributionService = $distributionService;
        $this->encryptionService = $encryptionService;
        $this->security = $security;
    }

    /**
     * @throws CouldNotConnectToMySqlServer
     * @throws CouldNotCreateDatabase
     * @throws CouldNotCreateDatabaseUser
     * @throws CouldNotTransformEncryptedStringToJson
     * @throws Exception
     */
    public function __invoke(CreateDistributionDatabaseCommand $command): void
    {
        $distribution = $command->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $databaseInformation = new DistributionDatabaseInformation($distribution);

        $databaseInformation->setUsername($this->encryptionService, $databaseInformation::USERNAME_PREPEND . bin2hex(random_bytes(10)));
        $databaseInformation->setPassword($this->encryptionService, bin2hex(random_bytes(32)));

        $distribution->setDatabaseInformation($databaseInformation);

        $this->distributionService->createDatabase($databaseInformation);
        $this->distributionService->createMysqlUser($databaseInformation, $this->encryptionService);

        $this->em->persist($distribution);
        $this->em->persist($databaseInformation);
        $this->em->flush();
    }
}

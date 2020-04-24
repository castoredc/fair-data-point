<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Connection\DistributionDatabaseInformation;
use App\Connection\DistributionService;
use App\Encryption\EncryptionService;
use App\Exception\CouldNotConnectToMySqlServer;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Message\Distribution\CreateDistributionDatabaseCommand;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function bin2hex;
use function random_bytes;

class CreateDistributionDatabaseCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var DistributionService */
    private $distributionService;

    /** @var EncryptionService */
    private $encryptionService;

    public function __construct(EntityManagerInterface $em, DistributionService $distributionService, EncryptionService $encryptionService)
    {
        $this->em = $em;
        $this->distributionService = $distributionService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @throws CouldNotConnectToMySqlServer
     * @throws CouldNotCreateDatabase
     * @throws CouldNotCreateDatabaseUser
     * @throws CouldNotTransformEncryptedStringToJson
     * @throws Exception
     */
    public function __invoke(CreateDistributionDatabaseCommand $message): void
    {
        $distribution = $message->getDistribution();

        $databaseInformation = new DistributionDatabaseInformation($distribution);

        $databaseInformation->setUsername($this->encryptionService, $databaseInformation::USERNAME_PREPEND . bin2hex(random_bytes(13)));
        $databaseInformation->setPassword($this->encryptionService, bin2hex(random_bytes(32)));

        $distribution->setDatabaseInformation($databaseInformation);

        $this->distributionService->createDatabase($databaseInformation);
        $this->distributionService->createMysqlUser($databaseInformation, $this->encryptionService);

        $this->em->persist($distribution);
        $this->em->persist($databaseInformation);
        $this->em->flush();
    }
}

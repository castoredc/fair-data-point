<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Connection\DistributionDatabaseInformation;
use App\Connection\DistributionService;
use App\Encryption\EncryptionService;
use App\Message\Distribution\CreateDistributionDatabaseCommand;
use Doctrine\ORM\EntityManagerInterface;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\RandomGenerator\Php7RandomGenerator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

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

    public function __invoke(CreateDistributionDatabaseCommand $message): void
    {
        $distribution = $message->getDistribution();

        $databaseInformation = new DistributionDatabaseInformation($distribution);

        $generator = new ComputerPasswordGenerator();
        $generator->setRandomGenerator(new Php7RandomGenerator());
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_LENGTH, 13);

        $databaseInformation->setUsername($this->encryptionService, $databaseInformation::USERNAME_PREPEND . $generator->generatePassword());

        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, true);
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_LENGTH, 32);

        $databaseInformation->setPassword($this->encryptionService, $generator->generatePassword());

        $distribution->setDatabaseInformation($databaseInformation);

        $this->distributionService->createDatabase($databaseInformation);
        $this->distributionService->createMysqlUser($databaseInformation, $this->encryptionService);

        $this->em->persist($distribution);
        $this->em->persist($databaseInformation);
        $this->em->flush();
    }
}

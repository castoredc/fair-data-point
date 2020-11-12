<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\CSV\CreateCSVDistributionCommand;
use App\Command\Distribution\RDF\CreateRDFDistributionCommand;
use App\CommandHandler\Distribution\CreateDistributionCommandHandler;
use App\Entity\Connection\DistributionDatabaseInformation;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Exception\CouldNotConnectToMySqlServer;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Exception\LanguageNotFound;
use Exception;

class CreateRDFDistributionCommandHandler extends CreateDistributionCommandHandler
{
    /**
     * @throws LanguageNotFound
     * @throws Exception
     */
    public function __invoke(CreateRDFDistributionCommand $command): Distribution
    {
        $distribution = $this->handleDistributionCreation($command);

        $dataModel = $this->em->getRepository(DataModel::class)->find($command->getDataModelId());

        $contents = new RDFDistribution(
            $distribution,
            $command->getAccessRights(),
            false
        );

        $contents->setDataModel($dataModel);
        $contents->setCurrentDataModelVersion($dataModel->getLatestVersion());

        $distribution->setContents($contents);

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->flush();

        try {
            $this->createDatabase($distribution);
        } catch (Exception $e) {
            // Rollback
            $this->em->remove($distribution);
            $this->em->remove($contents);
            $this->em->flush();

            throw $e;
        }

        return $distribution;
    }

    /**
     * @throws CouldNotConnectToMySqlServer
     * @throws CouldNotCreateDatabase
     * @throws CouldNotCreateDatabaseUser
     * @throws CouldNotTransformEncryptedStringToJson
     * @throws Exception
     */
    private function createDatabase(Distribution $distribution): void
    {
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

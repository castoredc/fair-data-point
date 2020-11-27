<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\UpdateRDFDistributionCommand;
use App\CommandHandler\Distribution\UpdateDistributionCommandHandler;
use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Exception\InvalidDataModelVersion;
use App\Exception\LanguageNotFound;
use function assert;

class UpdateRDFDistributionCommandHandler extends UpdateDistributionCommandHandler
{
    /**
     * @throws LanguageNotFound
     */
    public function __invoke(UpdateRDFDistributionCommand $command): void
    {
        $distribution = $this->handleDistributionUpdate($command);
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        $contents = $distribution->getContents();

        assert($contents instanceof RDFDistribution);

        $dataModel = $this->em->getRepository(DataModel::class)->find($command->getDataModelId());
        $dataModelVersion = $this->em->getRepository(DataModelVersion::class)->find($command->getDataModelVersionId());

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

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->persist($study);
        $this->em->flush();
    }
}

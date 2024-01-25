<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\CSV;

use App\Command\Distribution\CSV\UpdateCSVDistributionCommand;
use App\CommandHandler\Distribution\UpdateDistributionCommandHandler;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Exception\InvalidDataModelVersion;
use App\Exception\LanguageNotFound;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class UpdateCSVDistributionCommandHandler extends UpdateDistributionCommandHandler
{
    /** @throws LanguageNotFound */
    public function __invoke(UpdateCSVDistributionCommand $command): void
    {
        $distribution = $this->handleDistributionUpdate($command);
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        $contents = $distribution->getContents();

        assert($contents instanceof CSVDistribution);

        $dataDictionary = $this->em->getRepository(DataDictionary::class)->find($command->getDataDictionaryId());
        $dataDictionaryVersion = $this->em->getRepository(DataDictionaryVersion::class)->find($command->getDataDictionaryVersionId());

        if ($dataDictionary === null || $dataDictionaryVersion === null || $dataDictionaryVersion->getDataDictionary() !== $dataDictionary) {
            throw new InvalidDataModelVersion();
        }

        if ($contents->getDataDictionary() !== $dataDictionary) {
            // Switched data model, remove mappings
            foreach ($study->getMappings() as $mapping) {
                $this->em->remove($mapping);
            }

            $study->getMappings()->clear();
        }

        $contents->setDataDictionary($dataDictionary);
        $contents->setCurrentDataDictionaryVersion($dataDictionaryVersion);

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->persist($study);
        $this->em->flush();
    }
}

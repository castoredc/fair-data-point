<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\CSV;

use App\Command\Distribution\CSV\CreateCSVDistributionCommand;
use App\CommandHandler\Distribution\CreateDistributionCommandHandler;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\FAIRData\Distribution;
use App\Exception\LanguageNotFound;

class CreateCSVDistributionCommandHandler extends CreateDistributionCommandHandler
{
    /** @throws LanguageNotFound */
    public function __invoke(CreateCSVDistributionCommand $command): Distribution
    {
        $distribution = $this->handleDistributionCreation($command);

        $contents = new CSVDistribution(
            $distribution,
            $command->getAccessRights(),
            false
        );

        $distribution->setContents($contents);

        $this->em->persist($distribution);
        $this->em->persist($contents);
        $this->em->flush();

        return $distribution;
    }
}

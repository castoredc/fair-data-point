<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\GetAgentAssociatedMetadataCountCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Study;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAgentAssociatedMetadataCountCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /** @return array<string, int> */
    public function __invoke(GetAgentAssociatedMetadataCountCommand $command): array
    {
        $studyRepository = $this->em->getRepository(Study::class);
        $catalogRepository = $this->em->getRepository(Catalog::class);
        $datasetRepository = $this->em->getRepository(Dataset::class);
        $distributionRepository = $this->em->getRepository(Distribution::class);

        return [
            'study' => $studyRepository->countByAgent($command->getAgent()),
            'catalog' => $catalogRepository->countByAgent($command->getAgent()),
            'dataset' => $datasetRepository->countByAgent($command->getAgent()),
            'distribution' => $distributionRepository->countByAgent($command->getAgent()),
        ];
    }
}

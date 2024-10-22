<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\GetPaginatedDatasetsCommand;
use App\Entity\FAIRData\Dataset;
use App\Entity\PaginatedResultCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPaginatedDatasetsCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(GetPaginatedDatasetsCommand $command): PaginatedResultCollection
    {
        $datasetRepository = $this->em->getRepository(Dataset::class);

        $count = $datasetRepository->countDatasets(
            $command->getCatalog(),
            null,
            $command->getAgent(),
            $command->getHideCatalogs(),
            $command->getUser()
        );

        $datasets = $datasetRepository->findDatasets(
            $command->getCatalog(),
            null,
            $command->getAgent(),
            $command->getHideCatalogs(),
            $command->getPerPage(),
            $command->getPage(),
            $command->getUser()
        );

        return new PaginatedResultCollection(
            $datasets,
            $command->getPage(),
            $command->getPerPage(),
            $count
        );
    }
}

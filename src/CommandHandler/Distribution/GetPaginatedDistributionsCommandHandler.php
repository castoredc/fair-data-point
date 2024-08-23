<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\GetPaginatedDistributionsCommand;
use App\Entity\FAIRData\Distribution;
use App\Entity\PaginatedResultCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPaginatedDistributionsCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(GetPaginatedDistributionsCommand $command): PaginatedResultCollection
    {
        $distributionRepository = $this->em->getRepository(Distribution::class);

        $count = $distributionRepository->countDistributions(
            $command->getCatalog(),
            $command->getDataset(),
            $command->getAgent(),
            $command->getUser()
        );
        $datasets = $distributionRepository->findDistributions(
            $command->getCatalog(),
            $command->getDataset(),
            $command->getAgent(),
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

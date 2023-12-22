<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\GetPaginatedDatasetsCommand;
use App\Entity\FAIRData\Dataset;
use App\Entity\PaginatedResultCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPaginatedDatasetsCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetPaginatedDatasetsCommand $command): PaginatedResultCollection
    {
        $datasetRepository = $this->em->getRepository(Dataset::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $count = $datasetRepository->countDatasets($command->getCatalog(), $command->getAgent(), $command->getHideCatalogs(), $isAdmin);
        $datasets = $datasetRepository->findDatasets($command->getCatalog(), $command->getAgent(), $command->getHideCatalogs(), $command->getPerPage(), $command->getPage(), $isAdmin);

        return new PaginatedResultCollection(
            $datasets,
            $command->getPage(),
            $command->getPerPage(),
            $count
        );
    }
}

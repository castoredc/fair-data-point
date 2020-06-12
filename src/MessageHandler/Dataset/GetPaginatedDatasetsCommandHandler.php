<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Api\Resource\Dataset\PaginatedDatasetsApiResource;
use App\Entity\FAIRData\Dataset;
use App\Entity\PaginatedResultCollection;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use App\Repository\DatasetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function ceil;
use Symfony\Component\Security\Core\Security;

class GetPaginatedDatasetsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetPaginatedDatasetsCommand $command): PaginatedResultCollection
    {
        /** @var DatasetRepository $datasetRepository */
        $datasetRepository = $this->em->getRepository(Dataset::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $count = $datasetRepository->countDatasets($command->getCatalog(), $command->getHideCatalogs(), $isAdmin);
        $datasets = $datasetRepository->findDatasets($command->getCatalog(), $command->getHideCatalogs(), $command->getPerPage(), $command->getPage(), $isAdmin);

        return new PaginatedResultCollection($datasets, $command->getPage(), $command->getPerPage(), $count);
    }
}

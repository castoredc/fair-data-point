<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Api\Resource\Dataset\PaginatedDatasetsApiResource;
use App\Entity\FAIRData\Dataset;
use App\Message\Dataset\GetPaginatedDatasetsCommand;
use App\Repository\DatasetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function ceil;

class GetPaginatedDatasetsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetPaginatedDatasetsCommand $message): PaginatedDatasetsApiResource
    {
        /** @var DatasetRepository $datasetRepository */
        $datasetRepository = $this->em->getRepository(Dataset::class);
        $count = $datasetRepository->countDatasets($message->getCatalog(), $message->getSearch(), $message->getStudyType(), $message->getMethodType(), $message->getCountry());
        $pages = (int) ceil($count / $message->getPerPage());
        $datasets = $datasetRepository->findDatasets($message->getCatalog(), $message->getSearch(), $message->getStudyType(), $message->getMethodType(), $message->getCountry(), $message->getPerPage(), $message->getPage());

        return new PaginatedDatasetsApiResource($datasets, $message->getPerPage(), $message->getPage(), $pages);
    }
}

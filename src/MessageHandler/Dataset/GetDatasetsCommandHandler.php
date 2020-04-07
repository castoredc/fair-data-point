<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Message\Dataset\GetDatasetsCommand;
use App\Repository\DatasetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetDatasetsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @return Dataset[] */
    public function __invoke(GetDatasetsCommand $message): array
    {
        /** @var DatasetRepository $datasetRepository */
        $datasetRepository = $this->em->getRepository(Dataset::class);

        return $datasetRepository->findDatasets($message->getCatalog(), $message->getSearch(), $message->getStudyType(), $message->getMethodType(), $message->getCountry(), null, null);
    }
}

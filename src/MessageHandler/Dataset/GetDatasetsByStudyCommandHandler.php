<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Entity\PaginatedResultCollection;
use App\Message\Dataset\GetDatasetsByStudyCommand;
use App\Message\Dataset\GetDatasetsCommand;
use App\Repository\DatasetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetDatasetsByStudyCommandHandler implements MessageHandlerInterface
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @return Dataset[] */
    public function __invoke(GetDatasetsByStudyCommand $command): PaginatedResultCollection
    {
        $datasets = $command->getStudy()->getDatasets();

        $results = [];

        foreach($datasets as $dataset)
        {
            if($this->security->isGranted('view', $dataset))
            {
                $results[] = $dataset;
            }
        }

        return new PaginatedResultCollection($results, 1, count($results), count($results));
    }
}

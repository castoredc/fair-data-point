<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\GetDatasetsByStudyCommand;
use App\Entity\PaginatedResultCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;
use function count;

#[AsMessageHandler]
class GetDatasetsByStudyCommandHandler
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(GetDatasetsByStudyCommand $command): PaginatedResultCollection
    {
        $datasets = $command->getStudy()->getDatasets();

        $results = [];

        foreach ($datasets as $dataset) {
            if (! $this->security->isGranted('view', $dataset)) {
                continue;
            }

            $results[] = $dataset;
        }

        return new PaginatedResultCollection(
            $results,
            1,
            count($results),
            count($results),
        );
    }
}

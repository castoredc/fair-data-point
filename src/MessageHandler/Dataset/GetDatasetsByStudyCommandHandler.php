<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Entity\PaginatedResultCollection;
use App\Message\Dataset\GetDatasetsByStudyCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function count;

class GetDatasetsByStudyCommandHandler implements MessageHandlerInterface
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

        return new PaginatedResultCollection($results, 1, count($results), count($results));
    }
}

<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\GetDistributionGenerationLogsCommand;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\PaginatedResultCollection;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetDistributionGenerationLogsCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(GetDistributionGenerationLogsCommand $command): PaginatedResultCollection
    {
        $distribution = $command->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $repository = $this->em->getRepository(DistributionGenerationLog::class);

        $count = $repository->countLogs($distribution, $isAdmin);
        $logs = $repository->findLogs($distribution, $command->getPerPage(), $command->getPage(), $isAdmin);

        return new PaginatedResultCollection($logs, $command->getPage(), $command->getPerPage(), $count);
    }
}

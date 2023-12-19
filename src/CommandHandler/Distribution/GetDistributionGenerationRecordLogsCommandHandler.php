<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\GetDistributionGenerationRecordLogsCommand;
use App\Entity\Data\Log\DistributionGenerationRecordLog;
use App\Entity\PaginatedResultCollection;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;

#[AsMessageHandler]
class GetDistributionGenerationRecordLogsCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetDistributionGenerationRecordLogsCommand $command): PaginatedResultCollection
    {
        $log = $command->getLog();
        $distribution = $log->getDistribution()->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $repository = $this->em->getRepository(DistributionGenerationRecordLog::class);

        $count = $repository->countLogs($log, $isAdmin);
        $logs = $repository->findLogs($log, $command->getPerPage(), $command->getPage(), $isAdmin);

        return new PaginatedResultCollection($logs, $command->getPage(), $command->getPerPage(), $count);
    }
}

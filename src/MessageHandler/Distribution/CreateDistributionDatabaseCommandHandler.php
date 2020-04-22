<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Connection\DistributionService;
use App\Message\Distribution\CreateDistributionDatabaseCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDistributionDatabaseCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var DistributionService */
    private $distributionService;

    public function __construct(EntityManagerInterface $em, DistributionService $distributionService)
    {
        $this->em = $em;
        $this->distributionService = $distributionService;
    }

    public function __invoke(CreateDistributionDatabaseCommand $message): void
    {
        $distribution = $message->getDistribution();

        $distribution->setDatabaseInformation();
        $databaseInformation = $distribution->getDatabaseInformation();

        $this->em->persist($distribution);
        $this->em->persist($databaseInformation);
        $this->em->flush();

        $this->distributionService->createDatabase($databaseInformation);
        $this->distributionService->createMysqlUser($databaseInformation);
    }
}

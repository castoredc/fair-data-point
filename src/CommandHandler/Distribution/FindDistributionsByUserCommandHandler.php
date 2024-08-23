<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\FindDistributionsByUserCommand;
use App\Entity\FAIRData\Distribution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FindDistributionsByUserCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /** @return Distribution[] */
    public function __invoke(FindDistributionsByUserCommand $command): array
    {
        $distributionRepository = $this->em->getRepository(Distribution::class);

        return $distributionRepository->findByUser($command->getUser());
    }
}

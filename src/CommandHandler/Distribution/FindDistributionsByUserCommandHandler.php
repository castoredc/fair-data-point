<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\FindDistributionsByUserCommand;
use App\Entity\FAIRData\Distribution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function array_merge;
use function array_unique;

class FindDistributionsByUserCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @return Distribution[] */
    public function __invoke(FindDistributionsByUserCommand $command): array
    {
        $distributionRepository = $this->em->getRepository(Distribution::class);

        return array_unique(array_merge(
            $distributionRepository->findByUser($command->getUser()),
            $distributionRepository->findPublicDistributions()
        ));
    }
}

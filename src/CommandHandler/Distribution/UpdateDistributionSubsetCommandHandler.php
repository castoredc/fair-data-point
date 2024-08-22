<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\UpdateDistributionSubsetCommand;
use App\Entity\Castor\CastorStudy;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DistributionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class UpdateDistributionSubsetCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @throws NoAccessPermission */
    public function __invoke(UpdateDistributionSubsetCommand $command): void
    {
        $distribution = $command->getDistribution();
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted(DistributionVoter::EDIT, $distribution)) {
            throw new NoAccessPermission();
        }

        $contents = $distribution->getContents();
        $contents->setDependencies($command->getDependencies());

        $this->em->persist($contents);
        $this->em->flush();
    }
}

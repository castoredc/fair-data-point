<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\UpdateDistributionSubsetCommand;
use App\Entity\Castor\CastorStudy;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class UpdateDistributionSubsetCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @throws NoAccessPermission
     */
    public function __invoke(UpdateDistributionSubsetCommand $command): void
    {
        $distribution = $command->getDistribution();
        $dataset = $distribution->getDataset();
        $study = $dataset->getStudy();
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $contents = $distribution->getContents();
        $contents->setDependencies($command->getDependencies());

        $this->em->persist($contents);
        $this->em->flush();
    }
}

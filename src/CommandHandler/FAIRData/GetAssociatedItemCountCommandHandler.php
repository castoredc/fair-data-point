<?php
declare(strict_types=1);

namespace App\CommandHandler\FAIRData;

use App\Command\FAIRData\GetAssociatedItemCountCommand;
use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\AssociatedItemCount;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Study;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetAssociatedItemCountCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(GetAssociatedItemCountCommand $command): AssociatedItemCount
    {
        $entity = $command->getEntity();
        $user = $this->security->getUser();
        assert($user instanceof User || $user === null);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $catalogRepository = $this->em->getRepository(Catalog::class);
        $datasetRepository = $this->em->getRepository(Dataset::class);
        $distributionRepository = $this->em->getRepository(Distribution::class);
        $studyRepository = $this->em->getRepository(Study::class);

        $count = new AssociatedItemCount();

        if ($entity instanceof FAIRDataPoint) {
            $count->addCount(
                ResourceType::catalog(),
                $catalogRepository->countCatalogs(
                    null,
                    null,
                    null,
                    $user
                )
            );
        } elseif ($entity instanceof Catalog) {
            $count->addCount(
                ResourceType::study(),
                $studyRepository->countStudies(
                    $entity,
                    null,
                    null,
                    $isAdmin,
                )
            );

            $count->addCount(
                ResourceType::dataset(),
                $datasetRepository->countDatasets(
                    $entity,
                    null,
                    null,
                    null,
                    $user
                )
            );
        } elseif ($entity instanceof Dataset) {
            $count->addCount(
                ResourceType::distribution(),
                $distributionRepository->countDistributions(
                    null,
                    $entity,
                    null,
                    $user,
                )
            );
        } elseif ($entity instanceof Study) {
            $count->addCount(
                ResourceType::dataset(),
                $datasetRepository->countDatasets(
                    null,
                    $entity,
                    null,
                    null,
                    $user
                )
            );
        }

        return $count;
    }
}

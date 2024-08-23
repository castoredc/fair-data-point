<?php
declare(strict_types=1);

namespace App\Repository\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\DataSpecificationPermission;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\FAIRData\Permission\CatalogPermission;
use App\Entity\FAIRData\Permission\DatasetPermission;
use App\Entity\FAIRData\Permission\DistributionPermission;
use App\Entity\Metadata\CatalogMetadata;
use App\Entity\Metadata\DatasetMetadata;
use App\Entity\Metadata\DistributionMetadata;
use App\Security\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class MetadataModelRepository extends EntityRepository
{
    /** @return MetadataModel[] */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('metadataModel')
            ->select('metadataModel');

        if (! $user->isAdmin()) {
            $qb = $qb->join(
                DataSpecificationPermission::class,
                'permission',
                Join::WITH,
                $qb->expr()->andX(
                    'permission.dataSpecification = metadataModel.id',
                    'permission.user = :user',
                )
            );
            $qb->setParameter('user', $user->getId());
        }

        return $qb->getQuery()->getResult();
    }

    private function getPermissionsQuery(QueryBuilder $qb, User $user): QueryBuilder
    {
        $qb
            ->join(
                MetadataModelVersion::class,
                'metadataModelVersion',
                Join::WITH,
                'metadataModelVersion.dataSpecification = metadataModel.id'
            )
            ->leftJoin(
                CatalogMetadata::class,
                'catalogMetadata',
                Join::WITH,
                'catalogMetadata.metadataModelVersion = metadataModelVersion.id'
            )
            ->leftJoin(
                DatasetMetadata::class,
                'datasetMetadata',
                Join::WITH,
                'datasetMetadata.metadataModelVersion = metadataModelVersion.id'
            )
            ->leftJoin(
                DistributionMetadata::class,
                'distributionMetadata',
                Join::WITH,
                'distributionMetadata.metadataModelVersion = metadataModelVersion.id'
            )
            ->leftJoin(
                CatalogPermission::class,
                'catalogPermission',
                Join::WITH,
                $qb->expr()->andX(
                    'catalogMetadata.catalog = catalogPermission.catalog',
                    'catalogPermission.user = :user',
                )
            )
            ->leftJoin(
                DatasetPermission::class,
                'datasetPermission',
                Join::WITH,
                $qb->expr()->andX(
                    'datasetMetadata.dataset = datasetPermission.dataset',
                    'datasetPermission.user = :user',
                )
            )
            ->leftJoin(
                DistributionPermission::class,
                'distributionPermission',
                Join::WITH,
                $qb->expr()->andX(
                    'distributionMetadata.distribution = distributionPermission.distribution',
                    'distributionPermission.user = :user',
                )
            )
            ->distinct()
            ->where(
                $qb->expr()->orX(
                    'catalogPermission.user IS NOT NULL',
                    'datasetPermission.user IS NOT NULL',
                    'distributionPermission.user IS NOT NULL'
                )
            );

        $qb->setParameter('user', $user->getId());

        return $qb;
    }

    /** @return MetadataModel[] */
    public function findInUseByEntitiesUserHasPermissionsTo(User $user): array
    {
        $qb = $this->createQueryBuilder('metadataModel')
            ->select('metadataModel');

        if (! $user->isAdmin()) {
            $qb = $this->getPermissionsQuery($qb, $user);
        }

        return $qb->getQuery()->getResult();
    }

    public function isInUseByEntitiesUserHasPermissionsTo(MetadataModel $metadataModel, User $user): bool
    {
        $qb = $this->createQueryBuilder('metadataModel')
            ->select('count(DISTINCT metadataModel.id)');

        if (! $user->isAdmin()) {
            $qb = $this->getPermissionsQuery($qb, $user);

            $qb->andWhere('metadataModel.id = :metadataModelId');
            $qb->setParameter('metadataModelId', $metadataModel->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}

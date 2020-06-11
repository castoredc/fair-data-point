<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\Study;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Entity\Metadata\DatasetMetadata;
use App\Entity\Metadata\StudyMetadata;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class DatasetRepository extends EntityRepository
{
    /**
     * @return Dataset[]
     */
    public function findDatasets(Catalog $catalog, ?int $perPage, ?int $page, bool $admin): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
                   ->select('dataset');
        $qb = $this->getDatasetQuery($qb, $catalog, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countDatasets(Catalog $catalog, bool $admin): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
                      ->select('count(dataset.id)');
        $qb = $this->getDatasetQuery($qb, $catalog, $admin);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param Catalog $catalog
     * @param bool    $admin
     *
     * @return QueryBuilder
     */
    private function getDatasetQuery(QueryBuilder $qb, Catalog $catalog, bool $admin): QueryBuilder
    {
        $qb->from(Dataset::class, 'dataset')
                   ->innerJoin('dataset.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
                   ->setParameter('catalog_id', $catalog->getId());

        $qb->leftJoin(DatasetMetadata::class, 'metadata', Join::WITH, 'metadata.dataset = dataset.id')
           ->leftJoin(DatasetMetadata::class, 'metadata2', Join::WITH, 'metadata2.dataset = dataset.id AND metadata.createdAt < metadata2.createdAt')
           ->where('metadata2.id IS NULL');

        if (! $admin) {
            $qb->andWhere('dataset.isPublished = 1');
        }

        $qb->orderBy('metadata.title', 'ASC');

        return $qb;
    }
}

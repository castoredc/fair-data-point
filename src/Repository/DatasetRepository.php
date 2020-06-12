<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\Metadata\DatasetMetadata;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class DatasetRepository extends EntityRepository
{
    /**
     * @param string[]|null $hideCatalogs
     *
     * @return Dataset[]
     */
    public function findDatasets(?Catalog $catalog, ?array $hideCatalogs, ?int $perPage, ?int $page, bool $admin): array
    {
        $qb = $this->createQueryBuilder('dataset')
                   ->select('dataset');
        $qb = $this->getDatasetQuery($qb, $catalog, $hideCatalogs, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string[]|null $hideCatalogs
     */
    public function countDatasets(?Catalog $catalog, ?array $hideCatalogs, bool $admin): int
    {
        $qb = $this->createQueryBuilder('dataset')
                      ->select('count(dataset.id)');

        $qb = $this->getDatasetQuery($qb, $catalog, $hideCatalogs, $admin);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param string[]|null $hideCatalogs
     */
    private function getDatasetQuery(QueryBuilder $qb, ?Catalog $catalog, ?array $hideCatalogs, bool $admin): QueryBuilder
    {
        if ($catalog !== null) {
            $qb->innerJoin('dataset.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
                ->setParameter('catalog_id', $catalog->getId());
        }

        $qb->leftJoin(DatasetMetadata::class, 'metadata', Join::WITH, 'metadata.dataset = dataset.id')
           ->leftJoin(DatasetMetadata::class, 'metadata2', Join::WITH, 'metadata2.dataset = dataset.id AND metadata.createdAt < metadata2.createdAt')
           ->where('metadata2.id IS NULL');

        if ($hideCatalogs !== null) {
            $qb->andWhere(':catalog_ids NOT MEMBER OF dataset.catalogs')
               ->setParameter('catalog_ids', $hideCatalogs);
        }

        if (! $admin) {
            $qb->andWhere('dataset.isPublished = 1');
        }

        $qb->orderBy('metadata.title', 'ASC');

        return $qb;
    }
}

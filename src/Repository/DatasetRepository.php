<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Permission\DatasetPermission;
use App\Entity\Metadata\DatasetMetadata;
use App\Entity\Study;
use App\Security\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function assert;

class DatasetRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = DatasetMetadata::class;
    protected const TYPE = 'dataset';

    /**
     * @param string[]|null $hideCatalogs
     *
     * @return Dataset[]
     */
    public function findDatasets(?Catalog $catalog, ?Agent $agent, ?array $hideCatalogs, ?int $perPage, ?int $page, ?User $user): array
    {
        $qb = $this->createQueryBuilder('dataset')
                   ->select('dataset');

        $qb = $this->getDatasetQuery($qb, $catalog, $agent, $hideCatalogs, $user);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    /** @param string[]|null $hideCatalogs */
    public function countDatasets(?Catalog $catalog, ?Agent $agent, ?array $hideCatalogs, ?User $user): int
    {
        $qb = $this->createQueryBuilder('dataset')
                      ->select('count(DISTINCT dataset.id)');

        $qb = $this->getDatasetQuery($qb, $catalog, $agent, $hideCatalogs, $user);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        } catch (NonUniqueResultException) {
            return 0;
        }
    }

    /** @param string[]|null $hideCatalogs */
    private function getDatasetQuery(QueryBuilder $qb, ?Catalog $catalog, ?Agent $agent, ?array $hideCatalogs, ?User $user): QueryBuilder
    {
        $qb = $this->getQuery($qb);

        if ($user !== null && ! $user->isAdmin()) {
            $qb->leftJoin(DatasetPermission::class, 'permission', Join::WITH, 'permission.dataset = dataset.id');

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq('permission.user', ':user_id'),
                $qb->expr()->eq('dataset.isPublished', '1')
            ));

            $qb->setParameter('user_id', $user->getId());
        } elseif ($user === null) {
            $qb->andWhere('dataset.isPublished = 1');
        }

        if ($catalog !== null) {
            $qb->leftJoin(Study::class, 'study', Join::WITH, 'study.id = dataset.study');
            $qb->leftJoin('dataset.catalogs', 'catalog_dataset', Join::WITH, 'catalog_dataset MEMBER OF dataset.catalogs');
            $qb->leftJoin('study.catalogs', 'catalog_study', Join::WITH, 'catalog_study MEMBER OF study.catalogs');
        }

        if ($hideCatalogs !== null) {
            $qb->andWhere(':catalog_ids NOT MEMBER OF dataset.catalogs')
                ->setParameter('catalog_ids', $hideCatalogs);
        }

        if ($catalog !== null) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq('catalog_dataset.id', ':catalog_id'),
                $qb->expr()->eq('catalog_study.id', ':catalog_id')
            ));
            $qb->setParameter('catalog_id', $catalog->getId());
        }

        if ($agent !== null) {
            $qb = $this->getAgentQuery($qb, $agent);
        }

        return $qb;
    }

    public function findBySlug(string $slug): ?Dataset
    {
        $slug = $this->findOneBy(['slug' => $slug]);
        assert($slug instanceof Dataset || $slug === null);

        return $slug;
    }

    /** @return Dataset[] */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('dataset')
            ->select('dataset');

        $qb = $this->getDatasetQuery($qb, null, null, null, $user);

        return $qb->getQuery()->getResult();
    }
}

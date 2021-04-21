<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\Metadata\DistributionMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class DistributionRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = DistributionMetadata::class;
    protected const TYPE = 'distribution';

    /**
     * @return Dataset[]
     */
    public function findDistributions(?Catalog $catalog, ?Dataset $dataset, ?Agent $agent, ?int $perPage, ?int $page): array
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('distribution');

        $qb = $this->getDistributionQuery($qb, $catalog, $dataset, $agent);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countDistributions(?Catalog $catalog, ?Dataset $dataset, ?Agent $agent): int
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('count(distribution.id)');

        $qb = $this->getDistributionQuery($qb, $catalog, $dataset, $agent);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    private function getDistributionQuery(QueryBuilder $qb, ?Catalog $catalog, ?Dataset $dataset, ?Agent $agent): QueryBuilder
    {
        $qb = $this->getQuery($qb);

        if ($dataset !== null) {
            $qb->andWhere('distribution.dataset = :dataset_id');
            $qb->setParameter('dataset_id', $dataset->getId());
        }

        if ($agent !== null) {
            $qb = $this->getAgentQuery($qb, $agent);
        }

        $qb->orderBy('metadata.title', 'ASC');

        return $qb;
    }
}

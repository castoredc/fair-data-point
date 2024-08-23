<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Permission\DistributionPermission;
use App\Entity\Metadata\DistributionMetadata;
use App\Security\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function assert;

class DistributionRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = DistributionMetadata::class;
    protected const TYPE = 'distribution';

    /** @return Distribution[] */
    public function findDistributions(?Catalog $catalog, ?Dataset $dataset, ?Agent $agent, ?int $perPage, ?int $page, ?User $user): array
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('distribution');

        $qb = $this->getDistributionQuery($qb, $catalog, $dataset, $agent, $user);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countDistributions(?Catalog $catalog, ?Dataset $dataset, ?Agent $agent, ?User $user): int
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('count(DISTINCT distribution.id)');

        $qb = $this->getDistributionQuery($qb, $catalog, $dataset, $agent, $user);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        } catch (NonUniqueResultException) {
            return 0;
        }
    }

    private function getDistributionQuery(QueryBuilder $qb, ?Catalog $catalog, ?Dataset $dataset, ?Agent $agent, ?User $user): QueryBuilder
    {
        $qb = $this->getQuery($qb);

        if ($user !== null && ! $user->isAdmin()) {
            $qb->leftJoin(DistributionPermission::class, 'permission', Join::WITH, 'permission.distribution = distribution.id');

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq('permission.user', ':user_id'),
                $qb->expr()->eq('distribution.isPublished', '1')
            ));

            $qb->setParameter('user_id', $user->getId());
        } elseif ($user === null) {
            $qb->andWhere('distribution.isPublished = 1');
        }

        if ($dataset !== null) {
            $qb->andWhere('distribution.dataset = :dataset_id');
            $qb->setParameter('dataset_id', $dataset->getId());
        }

        if ($agent !== null) {
            $qb = $this->getAgentQuery($qb, $agent);
        }

        return $qb;
    }

    /** @return Distribution[] */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('distribution');

        $qb = $this->getDistributionQuery($qb, null, null, null, $user);

        return $qb->getQuery()->getResult();
    }

    public function findBySlug(string $slug): ?Distribution
    {
        $slug = $this->findOneBy(['slug' => $slug]);
        assert($slug instanceof Distribution || $slug === null);

        return $slug;
    }
}

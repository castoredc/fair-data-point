<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Permission\DistributionContentsPermission;
use App\Entity\Metadata\DistributionMetadata;
use App\Security\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class DistributionRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = DistributionMetadata::class;
    protected const TYPE = 'distribution';

    /** @return Distribution[] */
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

    /** @return Distribution[] */
    public function findPublicDistributions(): array
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('distribution');

        $qb = $this->getDistributionQuery($qb, null, null, null);

        $qb->innerJoin(DistributionContents::class, 'contents');

        $qb->andWhere(
            $qb->expr()->eq('contents.isPublic', '1'),
        );

        return $qb->getQuery()->getResult();
    }

    /** @return Distribution[] */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('distribution')
            ->select('distribution');

        $qb = $this->getDistributionQuery($qb, null, null, null);

        $qb->innerJoin(DistributionContents::class, 'contents');
        $qb->innerJoin(DistributionContentsPermission::class, 'permission');

        $qb->andWhere(
            $qb->expr()->andX(
                $qb->expr()->eq('permission.user', ':user_id'),
            ),
        );

        $qb->setParameter('user_id', $user->getId());

        return $qb->getQuery()->getResult();
    }
}

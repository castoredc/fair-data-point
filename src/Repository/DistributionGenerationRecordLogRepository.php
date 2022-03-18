<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data\Log\DistributionGenerationLog;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class DistributionGenerationRecordLogRepository extends EntityRepository
{
    /** @return mixed */
    public function findLogs(
        DistributionGenerationLog $log,
        ?int $perPage,
        ?int $page,
        bool $admin
    ) {
        $qb = $this->createQueryBuilder('recordLog')->select('recordLog');
        $qb = $this->getLogQuery($qb, $log, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countLogs(DistributionGenerationLog $log, bool $admin): int
    {
        $qb = $this->createQueryBuilder('recordLog')->select('count(recordLog.id)');
        $qb = $this->getLogQuery($qb, $log, $admin);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    private function getLogQuery(
        QueryBuilder $qb,
        DistributionGenerationLog $log,
        bool $admin
    ): QueryBuilder {
        $qb->innerJoin('recordLog.log', 'log', Join::WITH, 'log.id = :log_id')
           ->setParameter('log_id', $log->getId());

        return $qb;
    }
}

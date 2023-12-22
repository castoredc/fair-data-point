<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Distribution;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class DistributionGenerationLogRepository extends EntityRepository
{
    public function findLogs(
        ?Distribution $distribution,
        ?int $perPage,
        ?int $page,
        bool $admin
    ): mixed {
        $qb = $this->createQueryBuilder('log')->select('log');
        $qb = $this->getLogQuery($qb, $distribution, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countLogs(?Distribution $distribution, bool $admin): int
    {
        $qb = $this->createQueryBuilder('log')->select('count(log.id)');
        $qb = $this->getLogQuery($qb, $distribution, $admin);

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
        ?Distribution $distribution,
        bool $admin
    ): QueryBuilder {
        if ($distribution !== null) {
            $qb->innerJoin('log.distribution', 'distribution', Join::WITH, 'distribution.id = :distribution_id')
               ->setParameter('distribution_id', $distribution->getContents()->getId());
        }

        $qb->orderBy('log.createdAt', 'DESC');

        return $qb;
    }
}

<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Metadata\Metadata;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function sprintf;

abstract class MetadataEnrichedEntityRepository extends EntityRepository
{
    protected const METADATA_CLASS = Metadata::class;
    protected const TYPE = 'entity';

    public function countByAgent(Agent $agent): int
    {
        $qb = $this->createQueryBuilder($this::TYPE)
            ->select(sprintf('count(%s.id)', $this::TYPE));

        $qb = $this->getAgentQuery($this->getQuery($qb), $agent);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    protected function getQuery(QueryBuilder $qb): QueryBuilder
    {
        $qb->leftJoin(
            $this::METADATA_CLASS,
            'metadata',
            Join::WITH,
            sprintf('metadata.%s = %s.id', $this::TYPE, $this::TYPE)
        )
        ->leftJoin(
            $this::METADATA_CLASS,
            'metadata2',
            Join::WITH,
            sprintf('metadata2.%s = %s.id AND metadata.createdAt < metadata2.createdAt', $this::TYPE, $this::TYPE)
        )
        ->where('metadata2.id IS NULL')
        ->andWhere(sprintf('%s.isArchived = 0', $this::TYPE));

        $qb->orderBy('metadata.title', 'ASC');

        return $qb;
    }

    protected function getAgentQuery(QueryBuilder $qb, Agent $agent): QueryBuilder
    {
        $qb->innerJoin('metadata.publishers', 'publisher')
            ->leftJoin(Department::class, 'department', Join::WITH, 'publisher.id = department.id')
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'department.organization = organization.id');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('department.id', ':agent_id'),
                $qb->expr()->eq('organization.id', ':agent_id'),
                $qb->expr()->eq('publisher.id', ':agent_id'),
            )
        );

        $qb->setParameter('agent_id', $agent->getId());

        return $qb;
    }
}

<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Metadata\Metadata;
use App\Entity\Metadata\MetadataValue;
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
            ->leftJoin(
                MetadataModelVersion::class,
                'metadataModelVersion',
                Join::WITH,
                'metadata.metadataModelVersion = metadataModelVersion.id'
            )
            ->leftJoin(
                Predicate::class,
                'predicate',
                Join::WITH,
                'predicate.metadataModel = metadataModelVersion.id'
            )
            ->leftJoin(
                Triple::class,
                'triple',
                Join::WITH,
                'triple.predicate = predicate.id'
            )
            ->leftJoin(
                MetadataModelGroup::class,
                'modelGroup',
                Join::WITH,
                $qb->expr()->andX(
                    'triple.group = modelGroup.id',
                    sprintf('modelGroup.resourceType = \'%s\'', $this::TYPE)
                ),
            )
            ->leftJoin(
                MetadataValue::class,
                'metadataValue',
                Join::WITH,
                'triple.object = metadataValue.node AND metadataValue.metadata = metadata.id'
            )
        ->where('metadata2.id IS NULL')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    sprintf('predicate.iri = \'%s\'', MetadataModelVersion::DCTERMS_TITLE),
                    'modelGroup.id IS NOT NULL',
                    'metadataValue IS NOT NULL'
                ),
                $qb->expr()->andX(
                    'predicate.iri IS NULL',
                    'metadataValue IS NULL'
                ),
            ))
        ->andWhere(sprintf('%s.isArchived = 0', $this::TYPE))
        ->orderBy('metadataValue.value');

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

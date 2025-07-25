<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Enum\StudySource;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Study;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function assert;

class StudyRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = StudyMetadata::class;
    protected const TYPE = 'study';

    /** @param string[]|null $hideCatalogs */
    public function findStudies(
        ?Catalog $catalog,
        ?Agent $agent,
        ?array $hideCatalogs,
        bool $includeUnpublished,
        ?int $perPage,
        ?int $page,
        bool $admin,
    ): mixed {
        $qb = $this->createQueryBuilder('study')
                   ->select('study');

        $qb = $this->getStudyQuery($qb, $catalog, $agent, $hideCatalogs, $includeUnpublished, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    /** @return Study[] */
    public function getByAgent(Agent $agent): array
    {
        $qb = $this->createQueryBuilder('study')
            ->select('study');

        $qb = $this->getStudyQuery($qb, null, $agent, null, false, false);

        return $qb->getQuery()->getResult();
    }

    /** @param string[]|null $hideCatalogs */
    public function countStudies(?Catalog $catalog, ?Agent $agent, ?array $hideCatalogs, bool $includeUnpublished, bool $admin): int
    {
        $qb = $this->createQueryBuilder('study')
                   ->select('count(study.id)');

        $qb = $this->getStudyQuery($qb, $catalog, $agent, $hideCatalogs, $includeUnpublished, $admin);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        } catch (NonUniqueResultException) {
            return 0;
        }
    }

    public function countByAgent(Agent $agent): int
    {
        return $this->countStudies(null, $agent, null, false, false);
    }

    /** @param string[]|null $hideCatalogs */
    private function getStudyQuery(
        QueryBuilder $qb,
        ?Catalog $catalog,
        ?Agent $agent,
        ?array $hideCatalogs,
        bool $includeUnpublished,
        bool $admin,
    ): QueryBuilder {
        $qb = $this->getQuery($qb);

        if ($catalog !== null) {
            $qb->innerJoin('study.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
                ->setParameter('catalog_id', $catalog->getId());
        }

        if ($hideCatalogs !== null) {
            $qb->andWhere(':catalog_ids NOT MEMBER OF study.catalogs')
               ->setParameter('catalog_ids', $hideCatalogs);
        }

        if (! $includeUnpublished) {
            $qb->andWhere('study.isPublished = 1');
        }

        if ($agent !== null) {
            $qb = $this->getAgentQuery($qb, $agent);
        }

        return $qb;
    }

    public function studyExists(StudySource $source, string $sourceId): bool
    {
        return $this->count([
            'source' => $source,
            'sourceId' => $sourceId,
        ]) > 0;
    }

    public function findStudyBySourceAndId(StudySource $source, string $sourceId): ?Study
    {
        $study = $this->findOneBy([
            'source' => $source,
            'sourceId' => $sourceId,
        ]);

        assert($study instanceof Study || $study === null);

        return $study;
    }

    public function findBySlug(string $slug): ?Study
    {
        $slug = $this->findOneBy(['slug' => $slug]);
        assert($slug instanceof Study || $slug === null);

        return $slug;
    }
}

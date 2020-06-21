<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudySource;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Study;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class StudyRepository extends EntityRepository
{
    /**
     * @param StudyType[]|null  $studyType
     * @param string[]|null     $hideCatalogs
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     *
     * @return mixed
     */
    public function findStudies(
        ?Catalog $catalog,
        ?array $hideCatalogs,
        ?string $search,
        ?array $studyType,
        ?array $methodType,
        ?array $country,
        ?int $perPage,
        ?int $page,
        bool $admin
    ) {
        $qb = $this->createQueryBuilder('study')
                   ->select('study');

        $qb = $this->getStudyQuery($qb, $catalog, $hideCatalogs, $search, $studyType, $methodType, $country, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param StudyType[]|null  $studyType
     * @param string[]|null     $hideCatalogs
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     */
    public function countStudies(?Catalog $catalog, ?array $hideCatalogs, ?string $search, ?array $studyType, ?array $methodType, ?array $country, bool $admin): int
    {
        $qb = $this->createQueryBuilder('study')
                   ->select('count(study.id)');

        $qb = $this->getStudyQuery($qb, $catalog, $hideCatalogs, $search, $studyType, $methodType, $country, $admin);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param string[]|null     $hideCatalogs
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     */
    private function getStudyQuery(
        QueryBuilder $qb,
        ?Catalog $catalog,
        ?array $hideCatalogs,
        ?string $search,
        ?array $studyType,
        ?array $methodType,
        ?array $country,
        bool $admin
    ): QueryBuilder {
        if ($catalog !== null) {
            $qb->innerJoin('study.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
               ->setParameter('catalog_id', $catalog->getId());
        }

        $qb->leftJoin(StudyMetadata::class, 'metadata', Join::WITH, 'metadata.study = study.id')
           ->leftJoin(StudyMetadata::class, 'metadata2', Join::WITH, 'metadata2.study = study.id AND metadata.createdAt < metadata2.createdAt')
           ->where('metadata2.id IS NULL');

        if ($hideCatalogs !== null) {
            $qb->andWhere(':catalog_ids NOT MEMBER OF study.catalogs')
               ->setParameter('catalog_ids', $hideCatalogs);
        }

        if (! $admin) {
            $qb->andWhere('study.isPublished = 1');
        }

        if ($search !== null) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('metadata.briefName', ':search'),
                    $qb->expr()->like('metadata.briefSummary', ':search'),
                    $qb->expr()->like('metadata.summary', ':search')
                )
            );
            $qb->setParameter('search', '%' . $search . '%');
        }

        if ($studyType !== null) {
            $qb->andWhere('metadata.type IN (:studyType)');
            $qb->setParameter('studyType', $studyType);
        }

        if ($methodType !== null) {
            $qb->andWhere('metadata.methodType IN (:methodType)');
            $qb->setParameter('methodType', $methodType);
        }

        if ($country !== null) {
            $qb->innerJoin('metadata.centers', 'agent')
               ->join(Department::class, 'department', Join::WITH, 'agent.id = department.id')
               ->join(Organization::class, 'organization', Join::WITH, 'department.organization = organization.id')
               ->andWhere('organization.country IN (:country)');

            $qb->setParameter('country', $country);
        }

        $qb->orderBy('metadata.briefName', 'ASC');

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
        return $this->findOneBy([
            'source' => $source,
            'sourceId' => $sourceId,
        ]);
    }
}

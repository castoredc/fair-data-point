<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Entity\Metadata\StudyMetadata;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class StudyRepository extends EntityRepository
{
    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     *
     * @return mixed
     */
    public function findStudies(
        ?Catalog $catalog,
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

        $qb = $this->getStudyQuery($qb, $catalog, $search, $studyType, $methodType, $country, $admin);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param StudyType[]|null  $studyType
     * @param MethodType[]|null $methodType
     * @param string[]|null     $country
     */
    public function countStudies(?Catalog $catalog, ?string $search, ?array $studyType, ?array $methodType, ?array $country, bool $admin): int
    {
        $qb = $this->createQueryBuilder('study')
                   ->select('count(study.id)');

        $qb = $this->getStudyQuery($qb, $catalog, $search, $studyType, $methodType, $country, $admin);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param Catalog|null $catalog
     * @param string|null  $search
     * @param array|null   $studyType
     * @param array|null   $methodType
     * @param array|null   $country
     * @param bool         $admin
     *
     * @return QueryBuilder
     */
    private function getStudyQuery(
        QueryBuilder $qb,
        ?Catalog $catalog,
        ?string $search,
        ?array $studyType,
        ?array $methodType,
        ?array $country,
        bool $admin
    ): QueryBuilder {
        $qb = $qb->join(Dataset::class, 'dataset', Join::WITH, 'dataset.study = study.id');

        if ($catalog !== null) {
            $qb->innerJoin('dataset.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
               ->setParameter('catalog_id', $catalog->getId());
        }

        $qb->leftJoin(StudyMetadata::class, 'metadata', Join::WITH, 'metadata.study = study.id')
           ->leftJoin(StudyMetadata::class, 'metadata2', Join::WITH, 'metadata2.study = study.id AND metadata.createdAt < metadata2.createdAt')
           ->where('metadata2.id IS NULL');

        if (! $admin) {
            $qb->andWhere('dataset.isPublished = 1');
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
}

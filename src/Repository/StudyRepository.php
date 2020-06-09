<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\Study;
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
        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;

        $qb = $this->createQueryBuilder('study')
                   ->select('study')
                   ->join(Dataset::class, 'dataset', Join::WITH, 'dataset.study = study.id');

        if ($catalog !== null) {
            $qb->innerJoin('dataset.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
               ->setParameter('catalog_id', $catalog->getId());
        }

        $qb->join(StudyMetadata::class, 'metadata', Join::WITH, 'metadata.study = study.id')
           ->leftJoin(StudyMetadata::class, 'metadata2', Join::WITH, 'metadata2.study = study.id AND metadata.created < metadata2.created')
           ->where('metadata2.id IS NULL');

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
    public function countStudies(?Catalog $catalog, ?string $search, ?array $studyType, ?array $methodType, ?array $country): int
    {
        $qb = $this->createQueryBuilder('study')
                    ->select('count(study.id)')
                    ->join(Dataset::class, 'dataset', Join::WITH, 'dataset.study = study.id');

        if ($catalog !== null) {
            $qb->innerJoin('dataset.catalogs', 'catalog', Join::WITH, 'catalog.id = :catalog_id')
               ->setParameter('catalog_id', $catalog->getId());
        }

        $qb->join(StudyMetadata::class, 'metadata', Join::WITH, 'metadata.study = study.id')
           ->leftJoin(StudyMetadata::class, 'metadata2', Join::WITH, 'metadata2.study = study.id AND metadata.created < metadata2.created')
           ->where('metadata2.id IS NULL');
           // ->andWhere('dataset.isPublished = 1');

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

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }
}

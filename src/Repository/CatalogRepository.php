<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\Metadata\CatalogMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class CatalogRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = CatalogMetadata::class;
    protected const TYPE = 'catalog';

    /** @return Catalog[] */
    public function findCatalogs(?Agent $agent, ?bool $acceptSubmissions, ?int $perPage, ?int $page): array
    {
        $qb = $this->createQueryBuilder('catalog')
            ->select('catalog');

        $qb = $this->getCatalogQuery($qb, $agent, $acceptSubmissions);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countCatalogs(?Agent $agent, ?bool $acceptSubmissions): int
    {
        $qb = $this->createQueryBuilder('catalog')
            ->select('count(catalog.id)');

        $qb = $this->getCatalogQuery($qb, $agent, $acceptSubmissions);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    private function getCatalogQuery(QueryBuilder $qb, ?Agent $agent, ?bool $acceptSubmissions): QueryBuilder
    {
        $qb = $this->getQuery($qb);

        if ($agent !== null) {
            $qb = $this->getAgentQuery($qb, $agent);
        }

        if ($acceptSubmissions !== null) {
            $qb = $qb->where('catalog.acceptSubmissions = :acceptSubmissions');
            $qb->setParameter('acceptSubmissions', $acceptSubmissions);
        }

        $qb->orderBy('metadata.title', 'ASC');

        return $qb;
    }
}

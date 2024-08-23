<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Permission\CatalogPermission;
use App\Entity\Metadata\CatalogMetadata;
use App\Security\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function assert;

class CatalogRepository extends MetadataEnrichedEntityRepository
{
    protected const METADATA_CLASS = CatalogMetadata::class;
    protected const TYPE = 'catalog';

    /** @return Catalog[] */
    public function findCatalogs(?Agent $agent, ?bool $acceptSubmissions, ?int $perPage, ?int $page, ?string $search, ?User $user): array
    {
        $qb = $this->createQueryBuilder('catalog')
            ->select('catalog');

        $qb = $this->getCatalogQuery($qb, $agent, $acceptSubmissions, $search, $user);

        $firstResult = $page !== null && $perPage !== null ? ($page - 1) * $perPage : 0;
        $qb->setFirstResult($firstResult);

        if ($perPage !== null) {
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countCatalogs(?Agent $agent, ?bool $acceptSubmissions, ?string $search, ?User $user): int
    {
        $qb = $this->createQueryBuilder('catalog')
            ->select('count(DISTINCT catalog.id)');

        $qb = $this->getCatalogQuery($qb, $agent, $acceptSubmissions, $search, $user);

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        } catch (NonUniqueResultException) {
            return 0;
        }
    }

    private function getCatalogQuery(QueryBuilder $qb, ?Agent $agent, ?bool $acceptSubmissions, ?string $search, ?User $user): QueryBuilder
    {
        $qb = $this->getQuery($qb);

        if ($user !== null && ! $user->isAdmin()) {
            $qb = $qb->join(
                CatalogPermission::class,
                'permission',
                Join::WITH,
                $qb->expr()->andX(
                    'permission.catalog = catalog.id',
                    'permission.user = :user',
                )
            );
            $qb->setParameter('user', $user->getId());
        }

        if ($agent !== null) {
            $qb = $this->getAgentQuery($qb, $agent);
        }

        if ($acceptSubmissions !== null) {
            $qb = $qb->andWhere('catalog.acceptSubmissions = :acceptSubmissions');
            $qb->setParameter('acceptSubmissions', $acceptSubmissions);
        }

        return $qb;
    }

    public function findBySlug(string $slug): ?Catalog
    {
        $slug = $this->findOneBy(['slug' => $slug]);
        assert($slug instanceof Catalog || $slug === null);

        return $slug;
    }

    /** @return Catalog[] */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('catalog')
            ->select('catalog');

        $qb = $this->getCatalogQuery($qb, null, null, null, $user);

        return $qb->getQuery()->getResult();
    }
}

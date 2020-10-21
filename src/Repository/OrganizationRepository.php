<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Country;
use Doctrine\ORM\EntityRepository;

class OrganizationRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function findOrganizations(
        ?Country $country,
        string $search
    ) {
        $qb = $this->createQueryBuilder('organization')
            ->select('organization');

        $qb->where('organization.country = :country_id');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->like('organization.name', ':search'),
            )
        );

        $qb->setParameter('country_id', $country->getCode());
        $qb->setParameter('search', '%' . $search . '%');

        return $qb->getQuery()->getResult();
    }
}

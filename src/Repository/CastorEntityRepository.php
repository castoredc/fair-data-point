<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use Doctrine\ORM\EntityRepository;

class CastorEntityRepository extends EntityRepository
{
    public function findByIdAndStudy(CastorStudy $study, string $id): ?CastorEntity
    {
        return $this->findOneBy([
            'study' => $study,
            'id' => $id,
        ]);
    }

    /** @return CastorEntity[] */
    public function findByStudyAndType(CastorStudy $study, string $type): array
    {
        return $this->createQueryBuilder('entity')
                    ->select('entity')
                    ->where('entity.study = :study')
                    ->andWhere('entity INSTANCE OF :type')
                    ->setParameter('study', $study)
                    ->setParameter('type', $this->getEntityManager()->getClassMetadata($type))
                    ->getQuery()
                    ->getResult();
    }

    /** @return CastorEntity[] */
    public function findByStudyAndParent(CastorStudy $study, CastorEntity $parent): array
    {
        return $this->findBy([
            'study' => $study,
            'parent' => $parent,
        ]);
    }
}

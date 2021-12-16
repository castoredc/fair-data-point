<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use Doctrine\ORM\EntityRepository;
use function assert;

class CastorEntityRepository extends EntityRepository
{
    public function findByIdAndStudy(CastorStudy $study, string $id): ?CastorEntity
    {
        $entity = $this->findOneBy([
            'study' => $study,
            'id' => $id,
        ]);

        assert($entity instanceof CastorEntity || $entity === null);

        return $entity;
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
        /** @var CastorEntity[] $entities */
        $entities = $this->findBy([
            'study' => $study,
            'parent' => $parent,
        ]);

        return $entities;
    }
}

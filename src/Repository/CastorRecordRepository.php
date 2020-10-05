<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class CastorRecordRepository extends EntityRepository
{
    public function findByStudy(CastorStudy $study): ArrayCollection
    {
        $return = new ArrayCollection();

        /** @var Record[] $records */
        $records = $this->findBy(['study' => $study]);

        foreach ($records as $record) {
            $return->set($record->getId(), $record);
        }

        return $return;
    }
}

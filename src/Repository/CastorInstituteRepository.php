<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Institute;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class CastorInstituteRepository extends EntityRepository
{
    public function findByStudy(CastorStudy $study): ArrayCollection
    {
        $return = new ArrayCollection();

        /** @var Institute[] $institutes */
        $institutes = $this->findBy(['study' => $study]);

        foreach ($institutes as $institute) {
            $return->set($institute->getId(), $institute);
        }

        return $return;
    }
}

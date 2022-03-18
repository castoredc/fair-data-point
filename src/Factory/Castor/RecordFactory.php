<?php
declare(strict_types=1);

namespace App\Factory\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class RecordFactory
{
    /** @param array<mixed> $data */
    public function createFromCastorApiData(CastorStudy $study, ArrayCollection $institutes, array $data): Record
    {
        return new Record(
            $study,
            $institutes->get($data['_embedded']['institute']['id']),
            $data['record_id'],
            DateTimeImmutable::__set_state($data['created_on']),
            DateTimeImmutable::__set_state($data['updated_on'])
        );
    }
}

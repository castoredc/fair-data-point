<?php
declare(strict_types=1);

namespace App\Factory\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use DateTimeImmutable;

class RecordFactory
{
    /**
     * @param array<mixed> $data
     */
    public function createFromCastorApiData(CastorStudy $study, array $data): Record
    {
        return new Record(
            $study,
            $data['record_id'],
            DateTimeImmutable::__set_state($data['created_on']),
            DateTimeImmutable::__set_state($data['updated_on'])
        );
    }
}

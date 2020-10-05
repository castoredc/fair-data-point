<?php
declare(strict_types=1);

namespace App\Factory\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Institute;

class InstituteFactory
{
    /**
     * @param array<mixed> $data
     */
    public function createFromCastorApiData(CastorStudy $study, array $data): Institute
    {
        return new Institute(
            $study,
            $data['id'],
            $data['name'],
            $data['abbreviation'],
            $data['code'],
            $data['country_id'],
            $data['deleted']
        );
    }
}

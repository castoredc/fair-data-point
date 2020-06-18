<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\CastorStudy;

class GetRecordsCommand
{
    /** @var CastorStudy */
    private $study;

    public function __construct(CastorStudy $study)
    {
        $this->study = $study;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }
}

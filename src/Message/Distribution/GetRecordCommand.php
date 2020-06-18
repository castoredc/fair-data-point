<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Castor\CastorStudy;

class GetRecordCommand
{
    /** @var CastorStudy */
    private $study;

    /** @var string */
    private $recordId;

    public function __construct(CastorStudy $study, string $recordId)
    {
        $this->study = $study;
        $this->recordId = $recordId;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getRecordId(): string
    {
        return $this->recordId;
    }
}

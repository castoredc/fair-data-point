<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;

class InstanceData extends RecordData
{
    protected Record $record;

    public function __construct(Record $record, protected Instance $instance)
    {
        parent::__construct($record);
    }

    public function getInstance(): Instance
    {
        return $this->instance;
    }
}

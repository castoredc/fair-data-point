<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;

class InstanceData extends RecordData
{
    /** @var Record */
    protected $record;

    /** @var Instance */
    protected $instance;

    public function __construct(Record $record, Instance $instance)
    {
        parent::__construct($record);

        $this->instance = $instance;
    }

    public function getInstance(): Instance
    {
        return $this->instance;
    }
}

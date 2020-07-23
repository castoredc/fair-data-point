<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;
use Doctrine\Common\Collections\ArrayCollection;

class InstanceData extends RecordData
{
    /** @var Record */
    protected $record;

    /** @var Instance */
    protected $instance;

    /** @var ArrayCollection<string, FieldResult> */
    private $data;

    public function __construct(Record $record, Instance $instance)
    {
        parent::__construct($record);

        $this->instance = $instance;
        $this->data = new ArrayCollection();
    }

    public function getInstance(): Instance
    {
        return $this->instance;
    }
}

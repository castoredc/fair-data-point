<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Instances\Instance;
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

    public function getFieldResultByVariableName(string $variableName): ?FieldResult
    {
        return $this->data->get($variableName);
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getInstance(): Instance
    {
        return $this->instance;
    }

    public function addData(FieldResult $fieldResult): void
    {
        $this->data->set($fieldResult->getField()->getVariableName(), $fieldResult);
    }
}

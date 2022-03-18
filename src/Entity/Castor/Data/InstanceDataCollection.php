<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;
use Doctrine\Common\Collections\ArrayCollection;
use function assert;

abstract class InstanceDataCollection extends RecordData
{
    /** @var ArrayCollection<string, InstanceData> */
    private ArrayCollection $data;

    /** @var ArrayCollection<Instance> */
    private ArrayCollection $instances;

    public function __construct(Record $record)
    {
        parent::__construct($record);

        $this->data = new ArrayCollection();
        $this->instances = new ArrayCollection();
    }

    public function getMostRecentInstance(): ?Instance
    {
        if ($this->data->first() !== false) {
            $return = $this->data->first()->getInstance();

            foreach ($this->data as $instance) {
                if ($instance->getInstance()->getCreatedOn() <= $return->getCreatedOn()) {
                    continue;
                }

                $return = $instance->getInstance();
            }
        }

        return null;
    }

    public function getFieldResultByVariableName(string $variableName): ?FieldResult
    {
        $mostRecentInstance = $this->getMostRecentInstance();

        return $this->data->get($mostRecentInstance->getId())->getFieldResultByVariableName($variableName);
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data, CastorStudy $study, Record $record, ArrayCollection $instances): ?InstanceDataCollection
    {
        return null;
    }

    public function addInstanceData(Instance $instance, FieldResult $fieldResult): void
    {
        if (! $this->data->containsKey($instance->getId())) {
            $this->data->set($instance->getId(), new InstanceData($this->record, $instance));
            $this->instances->add($instance);
        }

        $instanceData = $this->data->get($instance->getId());
        assert($instanceData instanceof InstanceData);
        $instanceData->addData($fieldResult);
    }

    public function getInstances(): ArrayCollection
    {
        return $this->instances;
    }

    public function getInstanceData(Instance $instance): ?InstanceData
    {
        return $this->data->get($instance->getId());
    }
}

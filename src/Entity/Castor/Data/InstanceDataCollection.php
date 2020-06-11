<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Instances\Instance;
use App\Entity\Castor\Record;
use App\Entity\Castor\CastorStudy;
use Doctrine\Common\Collections\ArrayCollection;

abstract class InstanceDataCollection extends RecordData
{
    /** @var ArrayCollection<string, InstanceData> */
    private $data;

    public function __construct(Record $record)
    {
        parent::__construct($record);

        $this->data = new ArrayCollection();
    }

    public function getMostRecentInstance(): ?Instance
    {
        if ($this->data->first() !== false) {
            /** @var Instance $return */
            $return = $this->data->first()->getInstance();

            foreach ($this->data as $instance) {
                /** @var InstanceData $instance */
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

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, CastorStudy $study, Record $record, ArrayCollection $instances): ?InstanceDataCollection
    {
        return null;
    }

    public function addInstanceData(Instance $instance, FieldResult $fieldResult): void
    {
        if (! $this->data->containsKey($instance->getId())) {
            $this->data->set($instance->getId(), new InstanceData($this->record, $instance));
        }

        /** @var InstanceData $instanceData */
        $instanceData = $this->data->get($instance->getId());
        $instanceData->addData($fieldResult);
    }
}

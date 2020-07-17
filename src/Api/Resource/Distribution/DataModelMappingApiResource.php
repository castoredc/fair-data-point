<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Castor\CastorEntityApiResource;
use App\Api\Resource\Data\DataModelModuleApiResource;
use App\Api\Resource\Data\NodeApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\RDF\DataModelMapping;
use App\Entity\Data\RDF\DataModelModuleMapping;
use App\Entity\Data\RDF\DataModelNodeMapping;

class DataModelMappingApiResource implements ApiResource
{
    /** @var object */
    private $element;

    public function __construct(object $element)
    {
        $this->element = $element;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $return = [];

        if ($this->element instanceof DataModelNodeMapping || $this->element instanceof ValueNode) {
            $return['type'] = 'node';

            if ($this->element instanceof DataModelNodeMapping) {
                $return['node'] = (new NodeApiResource($this->element->getNode()))->toArray();
                $return['element'] = (new CastorEntityApiResource($this->element->getEntity()))->toArray();
            } elseif ($this->element instanceof ValueNode) {
                $return['node'] = (new NodeApiResource($this->element))->toArray();
                $return['element'] = null;
            }
        } elseif ($this->element instanceof DataModelModuleMapping || $this->element instanceof DataModelModule) {
            $return['type'] = 'module';

            if ($this->element instanceof DataModelModuleMapping) {
                $return['module'] = (new DataModelModuleApiResource($this->element->getModule()))->toArray();
                $return['element'] = (new CastorEntityApiResource($this->element->getEntity()))->toArray();
            } elseif ($this->element instanceof DataModelModule){
                $return['module'] = (new DataModelModuleApiResource($this->element))->toArray();
                $return['element'] = null;
            }
        }

        return $return;
    }
}

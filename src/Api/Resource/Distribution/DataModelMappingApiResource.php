<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Castor\CastorEntityApiResource;
use App\Api\Resource\Data\DataModelModuleApiResource;
use App\Api\Resource\Data\NodeApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Mapping\DataModelModuleMapping;
use App\Entity\Data\DataModel\Mapping\DataModelNodeMapping;
use App\Entity\Data\DataModel\Node\ValueNode;

class DataModelMappingApiResource implements ApiResource
{
    private object $element;

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
        $element = $this->element;

        if ($element instanceof DataModelNodeMapping) {
            $return['type'] = 'node';

            $return['node'] = (new NodeApiResource($element->getNode()))->toArray();
            $return['element'] = (new CastorEntityApiResource($element->getEntity()))->toArray();
        } elseif ($element instanceof ValueNode) {
            $return['type'] = 'node';

            $return['node'] = (new NodeApiResource($element))->toArray();
            $return['element'] = null;
        } elseif ($element instanceof DataModelModuleMapping) {
            $return['type'] = 'module';

            $return['module'] = (new DataModelModuleApiResource($element->getModule()))->toArray();
            $return['element'] = (new CastorEntityApiResource($element->getEntity()))->toArray();
        } elseif ($element instanceof DataModelModule) {
            $return['type'] = 'module';

            $return['module'] = (new DataModelModuleApiResource($element))->toArray();
            $return['element'] = null;
        }

        return $return;
    }
}

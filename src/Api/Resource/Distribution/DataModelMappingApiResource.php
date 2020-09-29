<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Castor\CastorEntityApiResource;
use App\Api\Resource\Data\DataModelModuleApiResource;
use App\Api\Resource\Data\NodeApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\RDF\DataModelModuleMapping;
use App\Entity\Data\RDF\DataModelNodeMapping;
use function assert;

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

        if ($element instanceof DataModelNodeMapping || $element instanceof ValueNode) {
            $return['type'] = 'node';

            if ($element instanceof DataModelNodeMapping) {
                $return['node'] = (new NodeApiResource($element->getNode()))->toArray();
                $return['element'] = (new CastorEntityApiResource($element->getEntity()))->toArray();
            } else {
                assert($element instanceof ValueNode);
                $return['node'] = (new NodeApiResource($element))->toArray();
                $return['element'] = null;
            }
        } elseif ($element instanceof DataModelModuleMapping || $element instanceof DataModelModule) {
            $return['type'] = 'module';

            if ($element instanceof DataModelModuleMapping) {
                $return['module'] = (new DataModelModuleApiResource($element->getModule()))->toArray();
                $return['element'] = (new CastorEntityApiResource($element->getEntity()))->toArray();
            } else {
                assert($element instanceof DataModelModule);
                $return['module'] = (new DataModelModuleApiResource($element))->toArray();
                $return['element'] = null;
            }
        }

        return $return;
    }
}

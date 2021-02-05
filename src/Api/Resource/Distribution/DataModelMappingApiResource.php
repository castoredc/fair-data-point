<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Castor\CastorEntityApiResource;
use App\Api\Resource\Data\DataModel\DataModelModuleApiResource;
use App\Api\Resource\Data\DataModel\NodeApiResource;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataSpecification\Mapping\ElementMapping;
use App\Entity\Data\DataSpecification\Mapping\GroupMapping;
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

        if ($element instanceof ElementMapping) {
            $return['type'] = 'node';
            $node = $element->getElement();
            assert($node instanceof Node);

            $return['node'] = (new NodeApiResource($node))->toArray();
            $return['elements'] = [(new CastorEntityApiResource($element->getEntity()))->toArray()];
            $return['transformed'] = false;
        } elseif ($element instanceof ValueNode) {
            $return['type'] = 'node';

            $return['node'] = (new NodeApiResource($element))->toArray();
            $return['elements'] = null;
        } elseif ($element instanceof GroupMapping) {
            $return['type'] = 'module';
            $group = $element->getGroup();
            assert($group instanceof DataModelGroup);

            $return['module'] = (new DataModelModuleApiResource($group))->toArray();
            $return['element'] = (new CastorEntityApiResource($element->getEntity()))->toArray();
        } elseif ($element instanceof DataModelGroup) {
            $return['type'] = 'module';

            $return['module'] = (new DataModelModuleApiResource($element))->toArray();
            $return['element'] = null;
        }

        return $return;
    }
}

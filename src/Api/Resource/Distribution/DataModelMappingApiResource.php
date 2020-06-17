<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Castor\CastorEntityApiResource;
use App\Api\Resource\Data\DataModelApiResource;
use App\Api\Resource\Data\NodeApiResource;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\RDF\DataModelMapping;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;

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

        if($this->element instanceof DataModelMapping) {
            $node = $this->element->getNode();
            $element = $this->element->getEntity();
        } else if($this->element instanceof ValueNode) {
            $node = $this->element;
            $element = null;
        }

        return [
            'node' => (new NodeApiResource($node))->toArray(),
            'element' => $element !== null ? (new CastorEntityApiResource($element))->toArray() : null
        ];
    }
}

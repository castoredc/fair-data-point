<?php
declare(strict_types=1);

namespace App\Api\Resource\Castor;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Data\DataModelApiResource;
use App\Api\Resource\Data\NodeApiResource;
use App\Entity\Castor\CastorEntity;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\RDF\DataModelMapping;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;

class CastorEntityApiResource implements ApiResource
{
    /** @var CastorEntity */
    private $entity;

    public function __construct(CastorEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->entity->getId(),
            'label' => $this->entity->getLabel(),
            'structureType' => $this->entity->getStructureType()
        ];

        return $data;
    }
}

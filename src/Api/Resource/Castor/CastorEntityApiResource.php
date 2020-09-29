<?php
declare(strict_types=1);

namespace App\Api\Resource\Castor;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\CastorEntity;

class CastorEntityApiResource implements ApiResource
{
    private CastorEntity $entity;

    public function __construct(CastorEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->entity->getId(),
            'label' => $this->entity->getLabel(),
            'structureType' => $this->entity->getStructureType() !== null ? $this->entity->getStructureType()->toString() : null,
        ];
    }
}

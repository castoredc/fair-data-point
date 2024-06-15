<?php
declare(strict_types=1);

namespace App\Api\Resource\Castor;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\CastorEntity;

class CastorEntityApiResource implements ApiResource
{
    public function __construct(private CastorEntity $entity)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->entity->getId(),
            'label' => $this->entity->getLabel(),
            'slug' => $this->entity->getSlug(),
            'structureType' => $this->entity->getStructureType()?->toString(),
        ];
    }
}

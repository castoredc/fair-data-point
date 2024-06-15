<?php
declare(strict_types=1);

namespace App\Api\Resource\Castor;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\CastorEntity;

class CastorEntitiesApiResource implements ApiResource
{
    /** @param CastorEntity[] $entities */
    public function __construct(private array $entities)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->entities as $entity) {
            $data[] = (new CastorEntityApiResource($entity))->toArray();
        }

        return $data;
    }
}

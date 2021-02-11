<?php
declare(strict_types=1);

namespace App\Api\Resource\Castor;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\CastorEntity;

class CastorEntitiesApiResource implements ApiResource
{
    /** @var CastorEntity[] */
    private array $entities;

    /**
     * @param CastorEntity[] $entities
     */
    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->entities as $entity) {
            $data[] = (new CastorEntityApiResource($entity))->toArray();
        }

        return $data;
    }
}

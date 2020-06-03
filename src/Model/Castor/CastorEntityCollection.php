<?php

namespace App\Model\Castor;

use App\Entity\Castor\CastorEntity;
use ArrayIterator;
use Countable;
use IteratorAggregate;

class CastorEntityCollection implements Countable, IteratorAggregate
{
    /**
     * An array containing the entries of this collection.
     *
     * @var array
     */
    private $entities;

    /**
     * @param CastorEntity[]|null $entities
     */
    public function __construct(?array $entities = null)
    {
        $this->entities = $entities ? $this->parseEntities($entities) : [];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->entities);
    }

    public function add(CastorEntity $entity): void
    {
        $this->entities[$entity->getId()] = $entity;
    }

    public function containsId(string $id)
    {
        return isset($this->entities[$id]) || array_key_exists($id, $this->entities);
    }

    public function contains(CastorEntity $entity)
    {
        return in_array($entity, $this->entities, true);
    }

    public function getById(string $id)
    {
        return $this->entities[$id] ?? null;
    }

    public function count(): int
    {
        return count($this->entities);
    }

    public function toArray(): array
    {
        return $this->entities;
    }

    /**
     * @param CastorEntity[] $entities
     * @return CastorEntity[]
     */
    private function parseEntities(array $entities): array
    {
        $newArray = [];

        foreach($entities as $entity)
        {
            $newArray[$entity->getId()] = $entity;
        }

        return $newArray;
    }
}
<?php
declare(strict_types=1);

namespace App\Model\Castor;

use App\Entity\Castor\CastorEntity;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use function array_key_exists;
use function count;
use function in_array;
use function strcasecmp;
use function usort;

class CastorEntityCollection implements Countable, IteratorAggregate
{
    /**
     * An array containing the entries of this collection.
     *
     * @var CastorEntity[]
     */
    private array $entities;

    /** @param CastorEntity[]|null $entities */
    public function __construct(?array $entities = null)
    {
        $this->entities = $entities !== null ? $this->parseEntities($entities) : [];
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->entities);
    }

    public function add(CastorEntity $entity): void
    {
        $this->entities[$entity->getId()] = $entity;
    }

    public function containsId(string $id): bool
    {
        return isset($this->entities[$id]) || array_key_exists($id, $this->entities);
    }

    public function contains(CastorEntity $entity): bool
    {
        return in_array($entity, $this->entities, true);
    }

    public function getById(string $id): ?CastorEntity
    {
        return $this->entities[$id] ?? null;
    }

    public function count(): int
    {
        return count($this->entities);
    }

    /** @return CastorEntity[] */
    public function toArray(): array
    {
        return $this->entities;
    }

    /**
     * @param CastorEntity[] $entities
     *
     * @return CastorEntity[]
     */
    private function parseEntities(array $entities): array
    {
        $newArray = [];

        foreach ($entities as $entity) {
            $newArray[$entity->getId()] = $entity;
        }

        return $newArray;
    }

    public function orderByLabel(): void
    {
        usort($this->entities, static function (CastorEntity $a, CastorEntity $b): int {
            return strcasecmp($a->getLabel(), $b->getLabel());
        });
    }
}

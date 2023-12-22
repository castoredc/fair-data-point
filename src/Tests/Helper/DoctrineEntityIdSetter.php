<?php
declare(strict_types=1);

/**
 * Allows overriding private properties (like an entity's `id` property) for testing purposes.
 */

namespace App\Tests\Helper;

use ReflectionException;
use ReflectionProperty;

trait DoctrineEntityIdSetter
{
    public function setEntityId(object $entity, mixed $id): void
    {
        $this->setEntityPrivatePropertyValue($entity, 'id', $id);
    }

    /** @throws ReflectionException */
    public function setEntityPrivatePropertyValue(object $entity, mixed $property, mixed $value): void
    {
        $reflection = new ReflectionProperty($entity::class, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($entity, $value);
    }
}

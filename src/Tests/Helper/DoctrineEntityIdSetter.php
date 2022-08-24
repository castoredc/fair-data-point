<?php
declare(strict_types=1);

/**
 * Allows overriding private properties (like an entity's `id` property) for testing purposes.
 */

namespace App\Tests\Helper;

use ReflectionException;
use ReflectionProperty;
use function get_class;

trait DoctrineEntityIdSetter
{
    /** @param mixed $id */
    public function setEntityId(object $entity, $id): void
    {
        $this->setEntityPrivatePropertyValue($entity, 'id', $id);
    }

    /**
     * @param mixed $property
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    public function setEntityPrivatePropertyValue(object $entity, $property, $value): void
    {
        $reflection = new ReflectionProperty(get_class($entity), $property);
        $reflection->setAccessible(true);
        $reflection->setValue($entity, $value);
    }
}

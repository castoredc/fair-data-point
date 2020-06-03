<?php
/**
 * @phpcs:ignoreFile
 */
declare(strict_types=1);

namespace App\Entity\Enum;

use Assert\Assertion;
use BadMethodCallException;
use Doctrine\Common\Inflector\Inflector;
use ReflectionClass;
use function in_array;
use function sprintf;
use function strtolower;

/**
 * Handy base class for VOs that only express different states.
 * You can indicate the different states by specifying them as constants. The
 * name of the constant will be all of the method names while the value of the
 * constant will be used as the string value when serializing or rehydrating.
 * Only string values are allowed.
 * When extending this class, don't forget the virtual method annotations:
 *      @ method static static mario()
 *      @ method static static luigi()
 *      @ method bool isMario()
 *      @ method bool isLuigi()
 *      class Nintendo extends Enum
 *      {
 *          private const MARIO = 'mario';
 *          private const LUIGI = 'luigi';
 *      }
 */
abstract class Enum
{
    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        Assertion::inArray($value, static::getConstants());

        $this->value = $value;
    }

    /**
     * @param array<mixed>  $arguments
     */
    public static function __callStatic(string $methodName, array $arguments): self
    {
        foreach (self::getConstants() as $option => $value) {
            if (strtolower(self::inflectConstantToMethodName($option)) === strtolower($methodName)) {
                return new static($value);
            }
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $methodName));
    }

    protected static function inflectConstantToMethodName(string $option): string
    {
        return Inflector::camelize($option);
    }

    /**
     * @param array<mixed> $arguments
     */
    public function __call(string $methodName, array $arguments): bool
    {
        foreach (self::getConstants() as $option => $value) {
            $expectedMethodName = 'is' . self::inflectConstantToMethodName($option);
            if (strtolower($expectedMethodName) === strtolower($methodName)) {
                return $this->isEqualTo(new static($value));
            }
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $methodName));
    }

    /**
     * Verifies if given enum has the same value and type
     * Weak comparison in PHP compares both the current state (so $this->value)
     * but also the current class, so two subclasses of enum with the state
     * won't match up just because they use the same string for some value.
     */
    public function isEqualTo(Enum $otherEnum): bool
    {
        /** @noinspection TypeUnsafeComparisonInspection,PhpNonStrictObjectEqualityInspection */
        return $this == $otherEnum;
    }

    /**
     * @return static
     */
    public static function fromString(string $someString): self
    {
        return new static($someString);
    }

    /**
     * @param array<string> $someStrings
     * @return array<static>
     */
    public static function fromArray(array $someStrings): array
    {
        $return = [];

        foreach($someStrings as $someString)
        {
            $return[] = static::fromString($someString);
        }

        return $return;
    }

    public static function canBeConstructedFromString(string $string): bool
    {
        return in_array($string, static::getConstants(), true);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return array<string>
     */
    private static function getConstants(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}

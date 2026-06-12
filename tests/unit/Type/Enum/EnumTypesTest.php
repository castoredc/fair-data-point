<?php
declare(strict_types=1);

namespace App\Tests\unit\Type\Enum;

use App\Entity\Enum\Enum;
use App\Entity\Enum\ResourceType;
use App\Type\Enum\EnumType;
use App\Type\Enum\ResourcesType;
use Assert\AssertionFailedException;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use function array_filter;
use function array_unique;
use function array_values;
use function basename;
use function glob;
use function in_array;
use function is_a;
use function is_string;
use function sprintf;

/**
 * Discovery-driven test guarding every custom enum DBAL type in src/Type/Enum
 * against regressions (for example after doctrine/dbal upgrades), without
 * needing a dedicated test file per type.
 */
final class EnumTypesTest extends TestCase
{
    private const ENUM_TYPE_DIRECTORY = __DIR__ . '/../../../../src/Type/Enum';

    /**
     * Classes in src/Type/Enum that intentionally do not extend EnumType.
     * They get dedicated tests below instead of the generic round-trip tests.
     */
    private const NON_ENUM_TYPE_CLASSES = [ResourcesType::class];

    /** Every class in the directory must be the abstract base, a known special case, or an EnumType subclass. */
    public function testEveryClassInEnumTypeDirectoryIsKnown(): void
    {
        foreach (self::discoverEnumTypeDirectoryClasses() as $class) {
            if ($class === EnumType::class || in_array($class, self::NON_ENUM_TYPE_CLASSES, true)) {
                continue;
            }

            self::assertTrue(
                is_a($class, EnumType::class, true),
                sprintf(
                    '%s lives in src/Type/Enum but does not extend %s; either fix the class or cover it explicitly in %s',
                    $class,
                    EnumType::class,
                    self::class,
                ),
            );
        }
    }

    /** @param class-string<EnumType> $typeClass */
    #[DataProvider('provideEnumTypeClasses')]
    public function testEveryAllowedValueRoundTripsInBothDirections(string $typeClass): void
    {
        $type = new $typeClass();
        $platform = new MySQLPlatform();
        $enumClass = self::enumClassOf($type);
        $allowedValues = self::allowedStringValues($enumClass);

        self::assertNotEmpty($allowedValues, sprintf('%s declares no string constants', $enumClass));

        foreach ($allowedValues as $value) {
            // Database value -> PHP value -> database value
            $phpValue = $type->convertToPHPValue($value, $platform);

            self::assertInstanceOf($enumClass, $phpValue, sprintf('%s should hydrate %s', $typeClass, $value));
            self::assertSame(
                $value,
                $type->convertToDatabaseValue($phpValue, $platform),
                sprintf('%s does not round-trip database value "%s"', $typeClass, $value),
            );

            // PHP value -> database value -> PHP value
            $enum = $enumClass::fromString($value);
            $databaseValue = $type->convertToDatabaseValue($enum, $platform);

            self::assertSame($value, $databaseValue);

            $rehydrated = $type->convertToPHPValue($databaseValue, $platform);

            self::assertInstanceOf(Enum::class, $rehydrated);
            self::assertTrue(
                $enum->isEqualTo($rehydrated),
                sprintf('%s does not round-trip PHP value "%s"', $typeClass, $value),
            );
        }
    }

    /** @param class-string<EnumType> $typeClass */
    #[DataProvider('provideEnumTypeClasses')]
    public function testNullAndEmptyStringHandling(string $typeClass): void
    {
        $type = new $typeClass();
        $platform = new MySQLPlatform();

        self::assertNull($type->convertToPHPValue(null, $platform));
        self::assertNull($type->convertToPHPValue('', $platform));

        // Note: the base class maps null to an empty string, not to SQL NULL.
        self::assertSame('', $type->convertToDatabaseValue(null, $platform));
        self::assertSame('', $type->convertToDatabaseValue('', $platform));
    }

    /** @param class-string<EnumType> $typeClass */
    #[DataProvider('provideEnumTypeClasses')]
    public function testRejectsAValueThatIsNotPartOfTheEnum(string $typeClass): void
    {
        $type = new $typeClass();
        $platform = new MySQLPlatform();

        $this->expectException(AssertionFailedException::class);

        $type->convertToPHPValue('value-that-no-enum-in-this-codebase-defines', $platform);
    }

    public function testResourcesTypeRoundTripsACommaSeparatedListOfResourceTypes(): void
    {
        $type = new ResourcesType();
        $platform = new MySQLPlatform();

        $phpValue = $type->convertToPHPValue('fdp,catalog,dataset,distribution,study', $platform);

        self::assertIsArray($phpValue);
        self::assertCount(5, $phpValue);
        self::assertContainsOnlyInstancesOf(ResourceType::class, $phpValue);
        self::assertSame(
            'fdp,catalog,dataset,distribution,study',
            $type->convertToDatabaseValue($phpValue, $platform),
        );
    }

    public function testResourcesTypeNullAndEmptyHandling(): void
    {
        $type = new ResourcesType();
        $platform = new MySQLPlatform();

        self::assertSame([], $type->convertToPHPValue(null, $platform));
        self::assertSame([], $type->convertToPHPValue('', $platform));
        self::assertNull($type->convertToDatabaseValue(null, $platform));
        self::assertSame('', $type->convertToDatabaseValue([], $platform));
    }

    public function testResourcesTypeRejectsAListContainingAnInvalidValue(): void
    {
        $type = new ResourcesType();
        $platform = new MySQLPlatform();

        $this->expectException(AssertionFailedException::class);

        $type->convertToPHPValue('fdp,value-that-no-enum-in-this-codebase-defines', $platform);
    }

    /** @return array<string, array{class-string<EnumType>}> */
    public static function provideEnumTypeClasses(): array
    {
        $cases = [];

        foreach (self::discoverEnumTypeDirectoryClasses() as $class) {
            if (! is_a($class, EnumType::class, true) || $class === EnumType::class) {
                continue;
            }

            $cases[$class] = [$class];
        }

        return $cases;
    }

    /** @return array<class-string> */
    private static function discoverEnumTypeDirectoryClasses(): array
    {
        $files = glob(self::ENUM_TYPE_DIRECTORY . '/*.php');

        if ($files === false || $files === []) {
            throw new RuntimeException('Could not find any enum types in ' . self::ENUM_TYPE_DIRECTORY);
        }

        $classes = [];

        foreach ($files as $file) {
            /** @var class-string $class */
            $class = 'App\\Type\\Enum\\' . basename($file, '.php');
            $classes[] = $class;
        }

        return $classes;
    }

    /** @return class-string<Enum> */
    private static function enumClassOf(EnumType $type): string
    {
        $enumClass = (new ReflectionClass($type))->getProperty('class')->getValue($type);

        if (! is_string($enumClass) || ! is_a($enumClass, Enum::class, true)) {
            throw new RuntimeException(
                sprintf('%s::$class should reference an %s subclass', $type::class, Enum::class),
            );
        }

        return $enumClass;
    }

    /**
     * All string constants of the enum class are accepted by Enum::fromString;
     * array constants (LABELS, TYPES, ...) are filtered out.
     *
     * @param class-string<Enum> $enumClass
     *
     * @return array<string>
     */
    private static function allowedStringValues(string $enumClass): array
    {
        $constants = (new ReflectionClass($enumClass))->getConstants();

        return array_values(array_unique(array_filter($constants, static fn ($value) => is_string($value))));
    }
}

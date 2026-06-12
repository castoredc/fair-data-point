<?php
declare(strict_types=1);

namespace App\Tests\unit\Entity\Enum;

use App\Entity\Enum\MetadataFieldType;
use App\Validator\Constraints\AgentArray;
use App\Validator\Constraints\LocalizedText;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function array_keys;

final class MetadataFieldTypeTest extends TestCase
{
    private const ALL_FIELD_TYPES = [
        'input',
        'inputLocale',
        'textarea',
        'textareaLocale',
        'ontologyConceptBrowser',
        'datePicker',
        'timePicker',
        'dateAndTimePicker',
        'checkbox',
        'checkboxes',
        'radioButtons',
        'dropdown',
        'languagePicker',
        'licensePicker',
        'countryPicker',
        'agentSelector',
    ];

    #[DataProvider('provideAllFieldTypes')]
    public function testEveryFieldTypeCanBeConstructedFromStringAndRoundTrips(string $fieldType): void
    {
        self::assertTrue(MetadataFieldType::canBeConstructedFromString($fieldType));
        self::assertSame($fieldType, MetadataFieldType::fromString($fieldType)->toString());
    }

    /** @return array<string, array<string>> */
    public static function provideAllFieldTypes(): array
    {
        $cases = [];

        foreach (self::ALL_FIELD_TYPES as $fieldType) {
            $cases[$fieldType] = [$fieldType];
        }

        return $cases;
    }

    #[DataProvider('provideHasOptionGroup')]
    public function testHasOptionGroup(string $fieldType, bool $expected): void
    {
        self::assertSame($expected, MetadataFieldType::fromString($fieldType)->hasOptionGroup());
    }

    /** @return array<string, array{string, mixed}> */
    public static function provideHasOptionGroup(): array
    {
        return self::casesForAllFieldTypes([
            'input' => false,
            'inputLocale' => false,
            'textarea' => false,
            'textareaLocale' => false,
            'ontologyConceptBrowser' => false,
            'datePicker' => false,
            'timePicker' => false,
            'dateAndTimePicker' => false,
            'checkbox' => false,
            'checkboxes' => true,
            'radioButtons' => true,
            'dropdown' => true,
            'languagePicker' => false,
            'licensePicker' => false,
            'countryPicker' => false,
            'agentSelector' => false,
        ]);
    }

    #[DataProvider('provideDefaultValues')]
    public function testGetDefaultValue(string $fieldType, mixed $expected): void
    {
        self::assertSame($expected, MetadataFieldType::fromString($fieldType)->getDefaultValue());
    }

    /** @return array<string, array{string, mixed}> */
    public static function provideDefaultValues(): array
    {
        $emptyLocalizedText = [
            [
                'text' => '',
                'language' => '',
            ],
        ];

        return self::casesForAllFieldTypes([
            'input' => '',
            'inputLocale' => $emptyLocalizedText,
            'textarea' => '',
            'textareaLocale' => $emptyLocalizedText,
            'ontologyConceptBrowser' => [],
            'datePicker' => '',
            'timePicker' => '',
            'dateAndTimePicker' => '',
            'checkbox' => false,
            'checkboxes' => [],
            'radioButtons' => '',
            'dropdown' => '',
            'languagePicker' => '',
            'licensePicker' => '',
            'countryPicker' => '',
            'agentSelector' => [],
        ]);
    }

    /** @param array{app: bool, type: string}|null $expected */
    #[DataProvider('provideValidators')]
    public function testGetValidator(string $fieldType, ?array $expected): void
    {
        self::assertSame($expected, MetadataFieldType::fromString($fieldType)->getValidator());
    }

    /** @return array<string, array{string, mixed}> */
    public static function provideValidators(): array
    {
        $plainString = [
            'app' => false,
            'type' => 'string',
        ];

        $localizedText = [
            'app' => true,
            'type' => LocalizedText::class,
        ];

        return self::casesForAllFieldTypes([
            'input' => $plainString,
            'inputLocale' => $localizedText,
            'textarea' => $plainString,
            'textareaLocale' => $localizedText,
            'ontologyConceptBrowser' => null,
            'datePicker' => $plainString,
            'timePicker' => $plainString,
            'dateAndTimePicker' => $plainString,
            'checkbox' => [
                'app' => false,
                'type' => 'bool',
            ],
            'checkboxes' => $plainString,
            'radioButtons' => $plainString,
            'dropdown' => $plainString,
            'languagePicker' => $plainString,
            'licensePicker' => $plainString,
            'countryPicker' => $plainString,
            'agentSelector' => [
                'app' => true,
                'type' => AgentArray::class,
            ],
        ]);
    }

    /** @param string[] $expected */
    #[DataProvider('provideDisplayTypes')]
    public function testGetDisplayTypes(string $fieldType, array $expected): void
    {
        self::assertSame($expected, MetadataFieldType::fromString($fieldType)->getDisplayTypes());
    }

    /** @return array<string, array{string, mixed}> */
    public static function provideDisplayTypes(): array
    {
        return self::casesForAllFieldTypes([
            'input' => ['heading', 'description', 'paragraph', 'image', 'link'],
            'inputLocale' => ['heading', 'description', 'paragraph'],
            'textarea' => ['description', 'paragraph'],
            'textareaLocale' => ['description', 'paragraph'],
            'ontologyConceptBrowser' => ['ontologyConcepts'],
            'datePicker' => ['date'],
            'timePicker' => ['time'],
            'dateAndTimePicker' => ['date', 'dateTime'],
            'checkbox' => ['yesNo'],
            'checkboxes' => ['list'],
            'radioButtons' => ['list'],
            'dropdown' => ['paragraph'],
            'languagePicker' => ['language'],
            'licensePicker' => ['license'],
            'countryPicker' => ['country'],
            'agentSelector' => ['agents'],
        ]);
    }

    /**
     * Builds a data provider covering every field type, guarding against missing or unknown cases.
     *
     * @param array<string, mixed> $expectationsPerFieldType
     *
     * @return array<string, array{string, mixed}>
     */
    private static function casesForAllFieldTypes(array $expectationsPerFieldType): array
    {
        self::assertSame(self::ALL_FIELD_TYPES, array_keys($expectationsPerFieldType));

        $cases = [];

        foreach ($expectationsPerFieldType as $fieldType => $expected) {
            $cases[$fieldType] = [$fieldType, $expected];
        }

        return $cases;
    }
}

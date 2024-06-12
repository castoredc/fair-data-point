<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

use App\Validator\Constraints\AgentArray;
use App\Validator\Constraints\LocalizedText;
use function in_array;

/**
 * @method static static input()
 * @method static static inputLocale()
 * @method static static textarea()
 * @method static static textareaLocale()
 * @method static static ontologyConceptBrowser()
 * @method static static datePicker()
 * @method static static timePicker()
 * // * @method static static dateAndTimePicker()
 * @method static static checkbox()
 * @method static static checkboxes()
 * @method static static radioButtons()
 * @method static static dropdown()
 * @method static static languagePicker()
 * @method static static licensePicker()
 * @method static static countryPicker()
 * @method static static agentSelector()
 * @method bool isInput()
 * @method bool isInputLocale()
 * @method bool isTextarea()
 * @method bool isTextareaLocale()
 * @method bool isOntologyConceptBrowser()
 * @method bool isDatePicker()
 * @method bool isTimePicker()
 * // * @method bool isDateAndTimePicker()
 * @method bool isCheckbox()
 * @method bool isCheckboxes()
 * @method bool isRadioButtons()
 * @method bool isDropdown()
 * @method bool isLanguagePicker()
 * @method bool isLicensePicker()
 * @method bool isCountryPicker()
 * @method bool isAgentSelector()
 * @inheritDoc
 */
class MetadataFieldType extends Enum
{
    public const INPUT = 'input';
    public const INPUT_LOCALE = 'inputLocale';
    public const TEXTAREA = 'textarea';
    public const TEXTAREA_LOCALE = 'textareaLocale';
    public const ONTOLOGY_CONCEPT_BROWSER = 'ontologyConceptBrowser';
    public const DATE_PICKER = 'datePicker';
    public const TIME_PICKER = 'timePicker';
//    public const DATE_AND_TIMEPICKER = 'dateAndTimePicker';
    public const CHECKBOX = 'checkbox';
    public const CHECKBOXES = 'checkboxes';
    public const RADIO_BUTTONS = 'radioButtons';
    public const DROPDOWN = 'dropdown';
    public const LANGUAGE_PICKER = 'languagePicker';
    public const LICENSE_PICKER = 'licensePicker';
    public const COUNTRY_PICKER = 'countryPicker';
    public const AGENT_SELECTOR = 'agentSelector';

    public const LABELS = [
        self::INPUT => 'Input',
        self::INPUT_LOCALE => 'Input (localized)',
        self::TEXTAREA => 'Textarea',
        self::TEXTAREA_LOCALE => 'Textarea (localized)',
        self::ONTOLOGY_CONCEPT_BROWSER => 'Ontology concept browser',
        self::DATE_PICKER => 'Date picker',
        self::TIME_PICKER => 'Time picker',
//        self::DATE_AND_TIMEPICKER => 'Date and Timepicker',
        self::CHECKBOX => 'Checkbox',
        self::CHECKBOXES => 'Checkboxes',
        self::RADIO_BUTTONS => 'Radio buttons',
        self::DROPDOWN => 'Dropdown',
        self::LANGUAGE_PICKER => 'Language picker',
        self::LICENSE_PICKER => 'License picker',
        self::COUNTRY_PICKER => 'Country picker',
        self::AGENT_SELECTOR => 'Agent selector',
    ];

    public const DEFAULT_VALUES = [
        self::INPUT => '',
        self::INPUT_LOCALE => [
            [
                'text' => '',
                'language' => '',
            ],
        ],
        self::TEXTAREA => '',
        self::TEXTAREA_LOCALE => [
            [
                'text' => '',
                'language' => '',
            ],
        ],
        self::ONTOLOGY_CONCEPT_BROWSER => [],
        self::DATE_PICKER => '',
        self::TIME_PICKER => '',
//        self::DATE_AND_TIMEPICKER => '',
        self::CHECKBOX => false,
        self::CHECKBOXES => [],
        self::RADIO_BUTTONS => '',
        self::DROPDOWN => '',
        self::LANGUAGE_PICKER => '',
        self::LICENSE_PICKER => '',
        self::COUNTRY_PICKER => '',
        self::AGENT_SELECTOR => [],
    ];

    public const VALIDATORS = [
        self::INPUT => [
            'app' => false,
            'type' => 'string',
        ],
        self::INPUT_LOCALE => [
            'app' => true,
            'type' => LocalizedText::class,
        ],
        self::TEXTAREA => [
            'app' => false,
            'type' => 'string',
        ],
        self::TEXTAREA_LOCALE => [
            'app' => true,
            'type' => LocalizedText::class,
        ],
        self::ONTOLOGY_CONCEPT_BROWSER => null,
        self::DATE_PICKER => [
            'app' => false,
            'type' => 'string',
        ],
        self::TIME_PICKER => [
            'app' => false,
            'type' => 'string',
        ],
//        self::DATE_AND_TIMEPICKER => '',
        self::CHECKBOX => [
            'app' => false,
            'type' => 'bool',
        ],
        self::CHECKBOXES => [
            'app' => false,
            'type' => 'string',
        ],
        self::RADIO_BUTTONS => [
            'app' => false,
            'type' => 'string',
        ],
        self::DROPDOWN => [
            'app' => false,
            'type' => 'string',
        ],
        self::LANGUAGE_PICKER => [
            'app' => false,
            'type' => 'string',
        ],
        self::LICENSE_PICKER => [
            'app' => false,
            'type' => 'string',
        ],
        self::COUNTRY_PICKER => [
            'app' => false,
            'type' => 'string',
        ],
        self::AGENT_SELECTOR => [
            'app' => true,
            'type' => AgentArray::class,
        ],
    ];

    public const TYPE_ANNOTATED = 'annotated';
    public const TYPE_PLAIN = 'plain';

    public const PLAIN_VALUE_TYPES = [
        XsdDataType::FLOAT => [
            self::INPUT,
        ],
        XsdDataType::DOUBLE => [
            self::INPUT,
        ],
        XsdDataType::DECIMAL => [
            self::INPUT,
        ],
        XsdDataType::INTEGER => [
            self::INPUT,
        ],
//        XsdDataType::DATE_TIME => [
//            self::DATE_AND_TIMEPICKER,
//        ],
        XsdDataType::DATE => [
            self::DATE_PICKER,
        ],
        XsdDataType::TIME => [
            self::TIME_PICKER,
        ],
        XsdDataType::G_DAY => [
            self::INPUT,
        ],
        XsdDataType::G_MONTH => [
            self::INPUT,
        ],
        XsdDataType::G_YEAR => [
            self::INPUT,
        ],
        XsdDataType::G_YEAR_MONTH => [
            self::INPUT,
        ],
        XsdDataType::G_MONTH_DAY => [
            self::INPUT,
        ],
        XsdDataType::STRING => [
            self::INPUT,
            self::TEXTAREA,
        ],
        XsdDataType::LANG_STRING => [
            self::INPUT_LOCALE,
            self::TEXTAREA_LOCALE,
        ],
        XsdDataType::BOOLEAN => [
            self::CHECKBOX,
        ],
        XsdDataType::URL => [
            self::INPUT,
        ],
    ];

    public const DISPLAY_TYPES = [
        self::INPUT => [
            MetadataDisplayType::HEADING,
            MetadataDisplayType::DESCRIPTION,
            MetadataDisplayType::PARAGRAPH,
            MetadataDisplayType::IMAGE,
            MetadataDisplayType::LINK,
        ],
        self::INPUT_LOCALE => [
            MetadataDisplayType::HEADING,
            MetadataDisplayType::DESCRIPTION,
            MetadataDisplayType::PARAGRAPH,
        ],
        self::TEXTAREA => [
            MetadataDisplayType::DESCRIPTION,
            MetadataDisplayType::PARAGRAPH,
        ],
        self::TEXTAREA_LOCALE => [
            MetadataDisplayType::DESCRIPTION,
            MetadataDisplayType::PARAGRAPH,
        ],
        self::ONTOLOGY_CONCEPT_BROWSER => [
            MetadataDisplayType::ONTOLOGY_CONCEPTS,
        ],
        self::DATE_PICKER => [
            MetadataDisplayType::DATE,
        ],
        self::TIME_PICKER => [
            MetadataDisplayType::TIME,
        ],
//        self::DATE_AND_TIMEPICKER => '',
        self::CHECKBOX => [
            MetadataDisplayType::YES_NO,
        ],
        self::CHECKBOXES => [
            MetadataDisplayType::LIST,
        ],
        self::RADIO_BUTTONS => [
            MetadataDisplayType::LIST,
        ],
        self::DROPDOWN => [
            MetadataDisplayType::PARAGRAPH,
        ],
        self::LANGUAGE_PICKER => [
            MetadataDisplayType::LANGUAGE,
        ],
        self::LICENSE_PICKER => [
            MetadataDisplayType::LICENSE,
        ],
        self::COUNTRY_PICKER => [
            MetadataDisplayType::COUNTRY,
        ],
        self::AGENT_SELECTOR => [
            MetadataDisplayType::AGENTS,
        ],
    ];

    public const ANNOTATED_VALUE_TYPES = [
        self::CHECKBOXES,
        self::RADIO_BUTTONS,
        self::DROPDOWN,
        self::AGENT_SELECTOR,
        self::ONTOLOGY_CONCEPT_BROWSER,
        self::LANGUAGE_PICKER,
        self::LICENSE_PICKER,
        self::COUNTRY_PICKER,
    ];

    public const HAS_OPTION_GROUP = [
        self::CHECKBOXES,
        self::RADIO_BUTTONS,
        self::DROPDOWN,
    ];

    public const TYPES = [
        self::TYPE_PLAIN => self::PLAIN_VALUE_TYPES,
        self::TYPE_ANNOTATED => self::ANNOTATED_VALUE_TYPES,
    ];

    public function hasOptionGroup(): bool
    {
        return in_array($this->toString(), self::HAS_OPTION_GROUP, true);
    }

    public function getDefaultValue(): mixed
    {
        return self::DEFAULT_VALUES[$this->toString()];
    }

    /** @return array{app: bool, type: string}|null */
    public function getValidator(): ?array
    {
        return self::VALIDATORS[$this->toString()];
    }

    /** @return string[]|null */
    public function getDisplayTypes(): ?array
    {
        return self::DISPLAY_TYPES[$this->toString()];
    }
}

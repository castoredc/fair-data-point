<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static input()
 * @method static static inputLocale()
 * @method static static textarea()
 * @method static static textareaLocale()
 * @method static static ontologyConceptBrowser()
 * @method static static datePicker()
 * @method static static timePicker()
 * @method static static dateAndTimePicker()
 * @method static static checkbox()
 * @method static static checkboxes()
 * @method static static radioButtons()
 * @method static static dropdown()
 * @method bool isInput()
 * @method bool isInputLocale()
 * @method bool isTextarea()
 * @method bool isTextareaLocale()
 * @method bool isOntologyConceptBrowser()
 * @method bool isDatePicker()
 * @method bool isTimePicker()
 * @method bool isDateAndTimePicker()
 * @method bool isCheckbox()
 * @method bool isCheckboxes()
 * @method bool isRadioButtons()
 * @method bool isDropdown()
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
    public const DATE_AND_TIMEPICKER = 'dateAndTimePicker';
    public const CHECKBOX = 'checkbox';
    public const CHECKBOXES = 'checkboxes';
    public const RADIO_BUTTONS = 'radioButtons';
    public const DROPDOWN = 'dropdown';

    public const LABELS = [
        self::INPUT => 'Input',
        self::INPUT_LOCALE => 'Input (localized)',
        self::TEXTAREA => 'Textarea',
        self::TEXTAREA_LOCALE => 'Textarea (localized)',
        self::ONTOLOGY_CONCEPT_BROWSER => 'Ontology concept browser',
        self::DATE_PICKER => 'Date picker',
        self::TIME_PICKER => 'Time picker',
        self::DATE_AND_TIMEPICKER => 'Date and Timepicker',
        self::CHECKBOX => 'Checkbox',
        self::CHECKBOXES => 'Checkboxes',
        self::RADIO_BUTTONS => 'Radio buttons',
        self::DROPDOWN => 'Dropdown',
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
        XsdDataType::DATE_TIME => [
            self::DATE_AND_TIMEPICKER,
        ],
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
    ];

    public const ANNOTATED_VALUE_TYPES = [
        self::ONTOLOGY_CONCEPT_BROWSER,
        self::CHECKBOXES,
        self::RADIO_BUTTONS,
        self::DROPDOWN,
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

    public function hasOptionGroup(): bool {
        return in_array($this->toString(), self::HAS_OPTION_GROUP, true);
    }
}

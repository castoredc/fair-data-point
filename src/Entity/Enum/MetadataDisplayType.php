<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static heading()
 * @method static static description()
 * @method static static paragraph()
 * @method static static ontologyConcepts()
 * @method static static ontologyConceptBrowser()
 * @method static static date()
 * @method static static time()
 * @method static static yesNo()
 * @method static static list()
 * @method static static text()
 * @method static static language()
 * @method static static license()
 * @method static static country()
 * @method static static agentSelector()
 * @method bool isHeading()
 * @method bool isDescription()
 * @method bool isParagraph()
 * @method bool isOntologyConcepts()
 * @method bool isOntologyConceptBrowser()
 * @method bool isDate()
 * @method bool isTime()
 * @method bool isYesNo()
 * @method bool isList()
 * @method bool isText()
 * @method bool isLanguage()
 * @method bool isLicense()
 * @method bool isCountry()
 * @method bool isAgents()
 * @inheritDoc
 */
class MetadataDisplayType extends Enum
{
    public const HEADING = 'heading';
    public const DESCRIPTION = 'description';
    public const PARAGRAPH = 'paragraph';
    public const ONTOLOGY_CONCEPTS = 'ontologyConcepts';
    public const DATE = 'date';
    public const TIME = 'time';
    public const DATE_TIME = 'dateTime';
    public const YES_NO = 'yesNo';
    public const LIST = 'list';
    public const LANGUAGE = 'language';
    public const LICENSE = 'license';
    public const COUNTRY = 'country';
    public const AGENTS = 'agents';
    public const IMAGE = 'image';
    public const LINK = 'link';

    public const ANY_TYPES = [
        self::HEADING,
        self::DESCRIPTION,
        self::PARAGRAPH,
        self::ONTOLOGY_CONCEPTS,
        self::DATE,
        self::TIME,
        self::DATE_TIME,
        self::YES_NO,
        self::LIST,
        self::LANGUAGE,
        self::LICENSE,
        self::COUNTRY,
        self::AGENTS,
        self::IMAGE,
        self::LINK,
    ];

    public const LABELS = [
        self::HEADING => 'Heading',
        self::DESCRIPTION => 'Description',
        self::PARAGRAPH => 'Paragraph',
        self::ONTOLOGY_CONCEPTS => 'Ontology concepts',
        self::DATE => 'Date',
        self::TIME => 'Time',
        self::DATE_TIME => 'Date and time',
        self::YES_NO => 'Yes/No',
        self::LIST => 'List',
        self::LANGUAGE => 'Language',
        self::LICENSE => 'License',
        self::COUNTRY => 'Country',
        self::AGENTS => 'Agents',
        self::IMAGE => 'Image',
        self::LINK => 'Link',
    ];
}

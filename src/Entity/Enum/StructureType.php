<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static study()
 * @method static static report()
 * @method static static survey()
 * @method bool isStudy()
 * @method bool isReport()
 * @method bool isSurvey()
 * @inheritDoc
 */
class StructureType extends Enum
{
    private const STUDY = 'study';
    private const REPORT = 'report';
    private const SURVEY = 'survey';
}

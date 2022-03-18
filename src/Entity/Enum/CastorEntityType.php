<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Form\FieldOption;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Castor\Structure\Report;
use App\Entity\Castor\Structure\Survey;

/**
 * @method static static field()
 * @method static static fieldOption()
 * @method static static fieldOptionGroup()
 * @method static static report()
 * @method static static survey()
 * @method bool isField()
 * @method bool isFieldOption()
 * @method bool isFieldOptionGroup()
 * @method bool isReport()
 * @method bool isSurvey()
 * @inheritDoc
 */
class CastorEntityType extends Enum
{
    private const FIELD = 'field';
    private const FIELD_OPTION = 'field_option';
    private const FIELD_OPTION_GROUP = 'field_option_group';
    private const REPORT = 'report';
    private const SURVEY = 'survey';

    public function getClassName(): string
    {
        if ($this->isField()) {
            return Field::class;
        }

        if ($this->isFieldOption()) {
            return FieldOption::class;
        }

        if ($this->isFieldOptionGroup()) {
            return FieldOptionGroup::class;
        }

        if ($this->isReport()) {
            return Report::class;
        }

        if ($this->isSurvey()) {
            return Survey::class;
        }

        return '';
    }
}

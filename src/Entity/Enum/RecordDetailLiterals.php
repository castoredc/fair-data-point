<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static recordId()
 * @method static static instituteId()
 * @method static static instituteName()
 * @method static static instituteAbbreviation()
 * @method static static instituteCode()
 * @method static static instituteCountryId()
 * @method bool isRecordId()
 * @method bool isInstituteId()
 * @method bool isInstituteName()
 * @method bool isInstituteAbbreviation()
 * @method bool isInstituteCode()
 * @method bool isInstituteCountryId()
 * @inheritDoc
 */
class RecordDetailLiterals extends Enum
{
    public const RECORD_ID = '##record_id##';
    public const INSTITUTE_ID = '##institute_id##';
    public const INSTITUTE_NAME = '##institute_name##';
    public const INSTITUTE_ABBREVIATION = '##institute_abbreviation##';
    public const INSTITUTE_CODE = '##institute_code##';
    public const INSTITUTE_COUNTRY_ID = '##institute_country_id##';
}

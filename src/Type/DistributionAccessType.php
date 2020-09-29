<?php
declare(strict_types=1);

namespace App\Type;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class DistributionAccessType extends AbstractEnumType
{
    public const PUBLIC = 1;
    public const STUDY_USERS = 2;
    public const STUDY_ADMIN = 3;

    /**
     * @inheritDoc
     * @var string[]
    */
    protected static $choices = [
        self::PUBLIC => 'Public',
        self::STUDY_USERS => 'Users with access to the study',
        self::STUDY_ADMIN => 'The person who created the study',
    ];
}

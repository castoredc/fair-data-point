<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static title()
 * @method static static description()
 * @method static static sidebar()
 * @method static static modal()
 * @method bool isTitle()
 * @method bool isDescription()
 * @method bool isSidebar()
 * @method bool isModal()
 * @inheritDoc
 */
class MetadataDisplayPosition extends Enum
{
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const SIDEBAR = 'sidebar';
    public const MODAL = 'modal';
}

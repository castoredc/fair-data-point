<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static email()
 * @method static static details()
 * @method static static affiliations()
 * @method bool isEmail()
 * @method bool isDetails()
 * @method bool isAffiliations()
 * @inheritDoc
 */
class Wizard extends Enum
{
    private const EMAIL = 'email';
    private const DETAILS = 'details';
    private const AFFILIATIONS = 'affiliations';

    private const ROUTES = [
        self::EMAIL => 'wizard_user_details',
        self::DETAILS => 'wizard_user_details',
        self::AFFILIATIONS => 'wizard_user_affiliations',
    ];

    public function getRoute(): string
    {
        return self::ROUTES[$this->toString()];
    }
}

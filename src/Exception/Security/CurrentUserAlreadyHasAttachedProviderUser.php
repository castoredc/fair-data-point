<?php
declare(strict_types=1);

namespace App\Exception\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CurrentUserAlreadyHasAttachedProviderUser extends AuthenticationException
{
    /** @var string */
    protected $message = 'The current user already has an attached user';
}

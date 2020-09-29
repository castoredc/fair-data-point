<?php
declare(strict_types=1);

namespace App\Exception\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CurrentUserAlreadyHasAttachedProviderUser extends AuthenticationException
{
    public function __construct()
    {
        parent::__construct('The current user already has an attached user');
    }
}

<?php
declare(strict_types=1);

namespace App\Security\Providers;

use App\Security\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

interface ProviderUser extends ResourceOwnerInterface
{
    public function getUser(): ?User;
}

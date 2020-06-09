<?php
declare(strict_types=1);

namespace App\Api\Resource;

abstract class RoleBasedApiResource implements ApiResource
{
    /** @var bool */
    protected $isAdmin;

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->isAdmin = $admin;
    }
}

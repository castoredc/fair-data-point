<?php
declare(strict_types=1);

namespace App\Model\Stardog;

use App\Entity\Encryption\SensitiveDataString;
use function json_encode;
use function str_split;

/**
 * @deprecated Stardog support was a proof of concept and is no longer in use.
 *             This Stardog-only code path is kept for reference, is not covered by tests,
 *             and should not be built upon.
 */
class AdminApiClient extends BaseApiClient
{
    public function addUser(SensitiveDataString $username, SensitiveDataString $password): void
    {
        $this->jsonRequest(
            '/admin/users',
            self::METHOD_POST,
            [
                'username' => $username->exposeAsString(),
                'password' => str_split($password->exposeAsString()),
            ]
        );
    }

    public function createDatabase(string $name): void
    {
        $this->multipartRequest(
            '/admin/databases',
            self::METHOD_POST,
            [
                'root' => json_encode(['dbname' => $name]),
            ]
        );
    }

    public function addRole(string $name): void
    {
        $this->jsonRequest(
            '/admin/roles',
            self::METHOD_POST,
            ['rolename' => $name]
        );
    }

    public function addRolePermissionForDatabase(string $role, string $action, string $database): void
    {
        $this->jsonRequest(
            '/admin/permissions/role/' . $role,
            self::METHOD_PUT,
            [
                'action' => $action,
                'resource_type' => '*',
                'resource' => [$database],
            ]
        );
    }

    public function addRoleToUser(SensitiveDataString $username, string $role): void
    {
        $this->jsonRequest(
            '/admin/users/' . $username->exposeAsString() . '/roles',
            self::METHOD_POST,
            ['rolename' => $role]
        );
    }
}

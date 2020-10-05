<?php
declare(strict_types=1);

namespace App\Factory\Castor;

use App\Security\Providers\Castor\CastorUser;

class CastorUserFactory
{
    /**
     * @param array<mixed> $data
     */
    public function createFromCastorApiData(array $data): CastorUser
    {
        return new CastorUser(
            $data['id'],
            $data['name_first'],
            $data['name_middle'] ?? null,
            $data['name_last'],
            $data['email_address']
        );
    }
}

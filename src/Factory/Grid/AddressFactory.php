<?php
declare(strict_types=1);

namespace App\Factory\Grid;

use App\Entity\Grid\Address;

class AddressFactory
{
    /** @param array<mixed> $data */
    public function createFromGridApiData(array $data): Address
    {
        return new Address(
            $data['number'],
            $data['primary'],
            $data['city'],
            $data['country']['code'],
            $data['coordinates']['lat'],
            $data['coordinates']['lng']
        );
    }
}

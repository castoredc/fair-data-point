<?php
declare(strict_types=1);

namespace App\Factory\Grid;

use App\Entity\Grid\Institute;
use App\Entity\Iri;

class InstituteFactory
{
    public function __construct(private AddressFactory $addressFactory)
    {
    }

    /** @param array<mixed> $data */
    public function createFromGridApiData(array $data): Institute
    {
        $institute = new Institute(
            $data['id'],
            $data['name']
        );

        foreach ($data['addresses'] as $address) {
            $institute->addAddress($this->addressFactory->createFromGridApiData($address));
        }

        foreach ($data['links'] as $link) {
            $institute->addLink(new Iri($link));
        }

        return $institute;
    }
}

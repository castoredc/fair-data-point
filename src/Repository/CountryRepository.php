<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FAIRData\Country;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class CountryRepository extends EntityRepository
{
    public function getAllCountries(): ArrayCollection
    {
        $return = new ArrayCollection();

        /** @var Country[] $countries */
        $countries = $this->findAll();

        foreach ($countries as $country) {
            $return->set($country->getCode(), $country);
        }

        return $return;
    }

    public function getAllCountriesWithCastorIds(): ArrayCollection
    {
        $return = new ArrayCollection();

        /** @var Country[] $countries */
        $countries = $this->findAll();

        foreach ($countries as $country) {
            $return->set($country->getCastorCountryId(), $country);
        }

        return $return;
    }
}

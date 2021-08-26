<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Study;

class CreateStudyCenterCommand
{
    private Study $study;

    private string $name;

    private string $country;

    private string $city;

    public function __construct(
        Study $study,
        string $name,
        string $country,
        string $city
    ) {
        $this->study = $study;
        $this->name = $name;
        $this->country = $country;
        $this->city = $city;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}

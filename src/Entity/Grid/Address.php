<?php
declare(strict_types=1);

namespace App\Entity\Grid;

class Address
{
    public function __construct(private int $number, private ?float $lat = null, private ?float $lng = null, private bool $primary, private string $city, private string $countryCode)
    {
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function hasCoordinates(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }
}

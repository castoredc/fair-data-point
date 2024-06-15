<?php
declare(strict_types=1);

namespace App\Command\Agent;

class FindOrganizationsCommand
{
    public function __construct(private string $country, private string $search)
    {
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getSearch(): string
    {
        return $this->search;
    }
}

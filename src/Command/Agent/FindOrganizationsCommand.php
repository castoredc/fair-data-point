<?php
declare(strict_types=1);

namespace App\Command\Agent;

class FindOrganizationsCommand
{
    private string $country;
    private string $search;

    public function __construct(string $country, string $search)
    {
        $this->country = $country;
        $this->search = $search;
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

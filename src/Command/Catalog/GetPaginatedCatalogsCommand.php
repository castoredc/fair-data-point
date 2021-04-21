<?php
declare(strict_types=1);

namespace App\Command\Catalog;

use App\Entity\FAIRData\Agent\Agent;

class GetPaginatedCatalogsCommand
{
    private ?Agent $agent;

    private ?string $search = null;

    private int $perPage;

    private int $page;

    public function __construct(?Agent $agent, ?string $search, int $perPage, int $page)
    {
        $this->agent = $agent;
        $this->search = $search;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}

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

    private ?bool $acceptSubmissions;

    public function __construct(?Agent $agent, ?string $search, int $perPage, int $page, ?bool $acceptSubmissions)
    {
        $this->agent = $agent;
        $this->search = $search;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->acceptSubmissions = $acceptSubmissions;
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

    public function getAcceptSubmissions(): ?bool
    {
        return $this->acceptSubmissions;
    }
}

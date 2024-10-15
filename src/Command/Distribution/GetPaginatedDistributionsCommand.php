<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Security\User;

class GetPaginatedDistributionsCommand
{
    public function __construct(
        private ?Catalog $catalog,
        private ?Dataset $dataset,
        private ?Agent $agent,
        private ?User $user,
        private int $perPage,
        private int $page,
        private ?string $search = null,
    ) {
    }

    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    public function getDataset(): ?Dataset
    {
        return $this->dataset;
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

    public function getUser(): ?User
    {
        return $this->user;
    }
}

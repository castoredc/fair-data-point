<?php
declare(strict_types=1);

namespace App\Command\Catalog;

use App\Entity\FAIRData\Agent\Agent;
use App\Security\User;

class GetPaginatedCatalogsCommand
{
    public function __construct(
        private int $perPage,
        private int $page,
        private ?string $search,
        private ?Agent $agent,
        private ?User $user,
        private ?bool $acceptSubmissions,
    ) {
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function getUser(): ?User
    {
        return $this->user;
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

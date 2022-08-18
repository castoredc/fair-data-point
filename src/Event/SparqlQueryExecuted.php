<?php
declare(strict_types=1);

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class SparqlQueryExecuted extends Event
{
    public const NAME = 'sparql.query.executed';
    private UserInterface $user;
    private string $query;
    private int $resultCount;
    private string $distributionId;

    public function __construct(string $distributionId, UserInterface $user, string $query, int $resultCount)
    {
        $this->distributionId = $distributionId;
        $this->user = $user;
        $this->query = $query;
        $this->resultCount = $resultCount;
    }


    public function getDistributionId(): string
    {
        return $this->distributionId;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
    public function getResultCount(): int
    {
        return $this->resultCount;
    }
}

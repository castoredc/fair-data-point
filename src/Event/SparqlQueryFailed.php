<?php
declare(strict_types=1);

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class SparqlQueryFailed extends Event
{
    public const NAME = 'sparql.query.failed';

    private string $distributionId;
    private UserInterface $user;
    private string $query;
    private string $error;

    public function __construct(string $distributionId, UserInterface $user, string $query, string $error)
    {
        $this->distributionId = $distributionId;
        $this->user = $user;
        $this->query = $query;
        $this->error = $error;
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

    public function getError(): string
    {
        return $this->error;
    }
}

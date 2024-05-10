<?php
declare(strict_types=1);

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class SparqlQueryFailed extends Event
{
    public const NAME = 'sparql.query.failed';

    public function __construct(private string $distributionId, private UserInterface $user, private string $query, private string $error)
    {
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

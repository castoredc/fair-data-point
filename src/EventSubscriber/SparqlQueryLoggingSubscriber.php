<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Data\Log\SparqlQueryLog;
use App\Event\SparqlQueryExecuted;
use App\Event\SparqlQueryFailed;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function assert;

final class SparqlQueryLoggingSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SparqlQueryExecuted::class => 'logSparqlQueryExecuted',
            SparqlQueryFailed::class => 'logSparqlQueryFailed',
        ];
    }

    public function logSparqlQueryExecuted(SparqlQueryExecuted $event): void
    {
        $user = $event->getUser();
        assert($user instanceof User);

        $log = SparqlQueryLog::successfulQuery(
            $event->getDistributionId(),
            $user->getId(),
            $user->getEmailAddress(),
            $event->getQuery(),
            $event->getResultCount()
        );

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function logSparqlQueryFailed(SparqlQueryFailed $event): void
    {
        $user = $event->getUser();
        assert($user instanceof User);

        $log = SparqlQueryLog::failedQuery(
            $event->getDistributionId(),
            $user->getId(),
            $user->getEmailAddress(),
            $event->getQuery(),
            $event->getError()
        );

        $this->entityManager->persist($log);
        $this->entityManager->flush();

    }
}

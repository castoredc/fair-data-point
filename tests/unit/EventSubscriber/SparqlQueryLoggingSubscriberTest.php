<?php
declare(strict_types=1);

namespace App\Tests\unit\EventSubscriber;

use App\Event\SparqlQueryFailed;
use App\EventSubscriber\SparqlQueryLoggingSubscriber;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use App\Tests\Helper\DoctrineEntityIdSetter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class SparqlQueryLoggingSubscriberTest extends TestCase
{
    use DoctrineEntityIdSetter;

    private const DISTRIBUTION_ID = '42089a2f-09e2-4cf2-a473-d450d5d0409d';
    private const USER_ID = '34b43023-b810-41ea-bbdf-1562c89f6434';
    private const FIRST_NAME = 'John';
    private const MIDDLE_NAME = 'F.';
    private const LAST_NAME = 'Doe';
    private const EMAILADDRESS = 'john.f.doe@testing.com';
    private const FAULTY_QUERY = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
SELECT * WHERE {
  syntax error
} LIMIT 100';

    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testShouldAddFailedLogOnFailure(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())
            ->method('persist');
        $entityManager->expects(self::once())
            ->method('flush');

        $subscriber = new SparqlQueryLoggingSubscriber($entityManager);
        $this->dispatcher->addSubscriber($subscriber);
        $user = (new User(null));
        $user->setCastorUser(
            new CastorUser(self::USER_ID, self::FIRST_NAME, self::MIDDLE_NAME, self::LAST_NAME, self::EMAILADDRESS)
        );
        $this->setEntityId($user, self::USER_ID);

        $this->dispatcher->dispatch(
            new SparqlQueryFailed(self::DISTRIBUTION_ID, $user, self::FAULTY_QUERY, 'Syntax error')
        );
    }
}

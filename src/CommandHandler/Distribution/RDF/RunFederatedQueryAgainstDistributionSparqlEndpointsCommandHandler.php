<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\RunFederatedQueryAgainstDistributionSparqlEndpointsCommand;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Event\SparqlQueryExecuted;
use App\Event\SparqlQueryFailed;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Graph\FederatedSparqlResponse;
use App\Service\Distribution\TripleStoreBasedDistributionService;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;
use function assert;

#[AsMessageHandler]
class RunFederatedQueryAgainstDistributionSparqlEndpointsCommandHandler
{
    public function __construct(private TripleStoreBasedDistributionService $distributionService, private EntityManagerInterface $em, private EncryptionService $encryptionService, private Security $security, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /** @throws Exception */
    public function __invoke(RunFederatedQueryAgainstDistributionSparqlEndpointsCommand $command): FederatedSparqlResponse
    {
        $results = new FederatedSparqlResponse();

        foreach ($command->getDistributionIds() as $distributionId) {
            $distribution = $this->em->getRepository(Distribution::class)->find($distributionId);
            assert($distribution instanceof Distribution || $distribution === null);

            if ($distribution === null) {
                throw new NotFound();
            }

            if (! $this->security->isGranted('access_data', $distribution)) {
                throw new NoAccessPermission();
            }

            $contents = $distribution->getContents();
            assert($contents instanceof RDFDistribution);

            try {
                $response = $this->distributionService->runQuery(
                    $command->getQuery(),
                    $distribution->getDatabaseInformation(),
                    $this->encryptionService
                );

                $response->setQueryUri($contents->getSparqlUrl());
                $results->addSparqlResponse($response);

                $this->eventDispatcher->dispatch(
                    new SparqlQueryExecuted(
                        $distribution->getId(),
                        $this->security->getUser(),
                        $command->getQuery(),
                        $results->getResultCount()
                    )
                );
            } catch (Throwable $e) {
                $this->eventDispatcher->dispatch(
                    new SparqlQueryFailed(
                        $distribution->getId(),
                        $this->security->getUser(),
                        $command->getQuery(),
                        $e->getMessage()
                    )
                );
            }
        }

        return $results;
    }
}

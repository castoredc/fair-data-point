<?php
declare(strict_types=1);

namespace App\CommandHandler\Terminology;

use App\Command\Terminology\FindOntologyConceptsCommand;
use App\Entity\Terminology\Ontology;
use App\Exception\OntologyNotFound;
use Castor\BioPortal\Api\ApiWrapper;
use Castor\BioPortal\Api\Helper\SearchTermOptions;
use Castor\BioPortal\Model\Concept;
use Castor\BioPortal\Model\Individual;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function array_merge;

class FindOntologyConceptsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private ApiWrapper $bioPortalApiWrapper;

    public function __construct(EntityManagerInterface $em, ApiWrapper $bioPortalApiWrapper)
    {
        $this->em = $em;
        $this->bioPortalApiWrapper = $bioPortalApiWrapper;
    }

    /** @return (Concept|Individual)[] */
    public function __invoke(FindOntologyConceptsCommand $command): array
    {
        $ontology = $this->em->getRepository(Ontology::class)->find($command->getOntologyId());

        if ($ontology === null) {
            throw new OntologyNotFound();
        }

        $searchOptions = new SearchTermOptions([$ontology->getBioPortalId()], false, true, null, 10, null);

        $results = $this->bioPortalApiWrapper->searchTerm($command->getQuery(), $searchOptions)->getCollection();

        if ($command->includeIndividuals()) {
            $individuals = $this->bioPortalApiWrapper->searchIndividual($command->getQuery(), $ontology->getBioPortalId());
            $results = array_merge($results, $individuals);
        }

        return $results;
    }
}

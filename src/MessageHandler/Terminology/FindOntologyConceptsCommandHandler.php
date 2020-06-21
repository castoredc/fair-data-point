<?php
declare(strict_types=1);

namespace App\MessageHandler\Terminology;

use App\Entity\Terminology\Ontology;
use App\Exception\OntologyNotFound;
use App\Message\Terminology\FindOntologyConceptsCommand;
use Castor\BioPortal\Api\ApiWrapper;
use Castor\BioPortal\Api\Helper\SearchTermOptions;
use Castor\BioPortal\Model\Concept;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FindOntologyConceptsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiWrapper  */
    private $bioPortalApiWrapper;

    public function __construct(EntityManagerInterface $em, ApiWrapper $bioPortalApiWrapper)
    {
        $this->em = $em;
        $this->bioPortalApiWrapper = $bioPortalApiWrapper;
    }

    /** @return Concept[] */
    public function __invoke(FindOntologyConceptsCommand $message): array
    {
        /** @var Ontology|null $ontology */
        $ontology = $this->em->getRepository(Ontology::class)->find($message->getOntologyId());

        if ($ontology === null) {
            throw new OntologyNotFound();
        }

        $searchOptions = new SearchTermOptions([$ontology->getBioPortalId()], false, true, null, 10, null);
        $results = $this->bioPortalApiWrapper->searchTerm($message->getQuery(), $searchOptions);

        return $results->getCollection();
    }
}

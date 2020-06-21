<?php
declare(strict_types=1);

namespace App\MessageHandler\Terminology;

use App\Entity\Iri;
use App\Entity\Terminology\Annotation;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\AnnotationAlreadyExists;
use App\Exception\NoAccessPermission;
use App\Exception\OntologyConceptNotFound;
use App\Exception\OntologyNotFound;
use App\Message\Terminology\AddAnnotationCommand;
use App\Repository\OntologyConceptRepository;
use Castor\BioPortal\Api\ApiWrapper;
use Castor\BioPortal\Api\Helper\SearchTermOptions;
use Castor\BioPortal\Model\Concept;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddAnnotationCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiWrapper  */
    private $bioPortalApiWrapper;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, ApiWrapper $bioPortalApiWrapper, Security $security)
    {
        $this->em = $em;
        $this->bioPortalApiWrapper = $bioPortalApiWrapper;
        $this->security = $security;
    }

    public function __invoke(AddAnnotationCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $entity = $command->getEntity();

        /** @var Ontology|null $ontology */
        $ontology = $this->em->getRepository(Ontology::class)->find($command->getOntologyId());

        if ($ontology === null) {
            throw new OntologyNotFound();
        }

        /** @var OntologyConceptRepository $ontologyConceptRepository */
        $ontologyConceptRepository = $this->em->getRepository(OntologyConcept::class);

        /** @var OntologyConcept|null $dbConcept */
        $dbConcept = $ontologyConceptRepository->findByOntologyAndCode($ontology, $command->getConceptCode());

        if ($dbConcept !== null) {
            if ($entity->hasAnnotation($dbConcept)) {
                throw new AnnotationAlreadyExists();
            }
        } else {
            $searchOptions = new SearchTermOptions([$ontology->getBioPortalId()], true, false, null, 1, null);
            $results = $this->bioPortalApiWrapper->searchTerm($command->getConceptCode(), $searchOptions);

            if ($results->getTotalCount() === 0) {
                throw new OntologyConceptNotFound();
            }

            /** @var Concept $concept */
            $concept = $results->getCollection()[0];

            $dbConcept = new OntologyConcept(new Iri((string) $concept->getId()), $concept->getNotation(), $ontology, $concept->getPrefLabel());

            $this->em->persist($dbConcept);
        }

        $annotation = new Annotation($entity, $dbConcept);
        $entity->addAnnotation($annotation);

        $this->em->persist($entity);
        $this->em->persist($annotation);

        $this->em->flush();
    }
}

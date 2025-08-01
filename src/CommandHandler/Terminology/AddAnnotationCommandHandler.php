<?php
declare(strict_types=1);

namespace App\CommandHandler\Terminology;

use App\Command\Terminology\AddAnnotationCommand;
use App\Entity\Iri;
use App\Entity\Terminology\Annotation;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\AnnotationAlreadyExists;
use App\Exception\NoAccessPermission;
use App\Exception\OntologyConceptNotFound;
use App\Exception\OntologyNotFound;
use App\Security\Authorization\Voter\StudyVoter;
use Castor\BioPortal\Api\ApiWrapper;
use Castor\BioPortal\Api\Helper\SearchTermOptions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function count;

#[AsMessageHandler]
class AddAnnotationCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private ApiWrapper $bioPortalApiWrapper, private Security $security)
    {
    }

    public function __invoke(AddAnnotationCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted(StudyVoter::EDIT, $study)) {
            throw new NoAccessPermission();
        }

        $entity = $command->getEntity();

        $ontology = $this->em->getRepository(Ontology::class)->find($command->getOntologyId());

        if ($ontology === null) {
            throw new OntologyNotFound();
        }

        $ontologyConceptRepository = $this->em->getRepository(OntologyConcept::class);
        $dbConcept = $ontologyConceptRepository->findByOntologyAndCode($ontology, $command->getConceptCode());

        if ($dbConcept !== null) {
            if ($entity->hasAnnotation($dbConcept)) {
                throw new AnnotationAlreadyExists();
            }
        } else {
            if ($command->getConceptType()->isIndividual()) {
                $results = $this->bioPortalApiWrapper->searchIndividual($command->getConceptCode(), $ontology->getBioPortalId());

                if (count($results) === 0) {
                    throw new OntologyConceptNotFound();
                }

                $concept = $results[0];

                $dbConcept = new OntologyConcept(new Iri((string) $concept->getId()), $concept->getId()->getBase(), $ontology, $concept->getLabel());
            } elseif ($command->getConceptType()->isConcept()) {
                $searchOptions = new SearchTermOptions([$ontology->getBioPortalId()], true, false, null, 1, null);
                $results = $this->bioPortalApiWrapper->searchTerm($command->getConceptCode(), $searchOptions);

                if ($results->getTotalCount() === 0) {
                    throw new OntologyConceptNotFound();
                }

                $concept = $results->getCollection()[0];

                $dbConcept = new OntologyConcept(new Iri((string) $concept->getId()), $concept->getNotation(), $ontology, $concept->getPrefLabel());
            }

            $this->em->persist($dbConcept);
        }

        $annotation = new Annotation($entity, $dbConcept);
        $entity->addAnnotation($annotation);

        $this->em->persist($entity);
        $this->em->persist($annotation);

        $this->em->flush();
    }
}

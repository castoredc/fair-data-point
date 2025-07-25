<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use Doctrine\ORM\EntityRepository;
use function assert;

class OntologyConceptRepository extends EntityRepository
{
    public function findByOntologyAndCode(Ontology $ontology, string $code): ?OntologyConcept
    {
        $concept = $this->findOneBy([
            'ontology' => $ontology,
            'code' => $code,
        ]);

        assert($concept instanceof OntologyConcept || $concept === null);

        return $concept;
    }
}

<?php
declare(strict_types=1);

namespace App\Command\Terminology;

use App\Entity\Castor\CastorEntity;
use App\Entity\Enum\OntologyConceptType;
use App\Entity\Study;

class AddAnnotationCommand
{
    public function __construct(private Study $study, private CastorEntity $entity, private string $ontologyId, private string $conceptCode, private OntologyConceptType $conceptType)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function setStudy(Study $study): void
    {
        $this->study = $study;
    }

    public function getEntity(): CastorEntity
    {
        return $this->entity;
    }

    public function setEntity(CastorEntity $entity): void
    {
        $this->entity = $entity;
    }

    public function getOntologyId(): string
    {
        return $this->ontologyId;
    }

    public function setOntologyId(string $ontologyId): void
    {
        $this->ontologyId = $ontologyId;
    }

    public function getConceptCode(): string
    {
        return $this->conceptCode;
    }

    public function setConceptCode(string $conceptCode): void
    {
        $this->conceptCode = $conceptCode;
    }

    public function getConceptType(): OntologyConceptType
    {
        return $this->conceptType;
    }
}

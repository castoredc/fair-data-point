<?php
declare(strict_types=1);

namespace App\Message\Terminology;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Study;

class AddAnnotationCommand
{
    /** @var Study */
    private $study;

    /** @var CastorEntity */
    private $entity;

    /** @var string */
    private $ontologyId;

    /** @var string */
    private $conceptCode;

    public function __construct(Study $study, CastorEntity $entity, string $ontologyId, string $conceptCode)
    {
        $this->study = $study;
        $this->entity = $entity;
        $this->ontologyId = $ontologyId;
        $this->conceptCode = $conceptCode;
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
}

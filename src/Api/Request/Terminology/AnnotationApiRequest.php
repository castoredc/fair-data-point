<?php
declare(strict_types=1);

namespace App\Api\Request\Terminology;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\CastorEntityType;
use App\Entity\Enum\OntologyConceptType;
use Symfony\Component\Validator\Constraints as Assert;

class AnnotationApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $entityType;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $entityId;

    /** @Assert\Type("string") */
    private ?string $entityParent = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $ontology;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $concept;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $conceptType;

    protected function parse(): void
    {
        $this->entityType = $this->getFromData('entityType');
        $this->entityId = $this->getFromData('entityId');
        $this->entityParent = $this->getFromData('entityParent');
        $this->ontology = $this->getFromData('ontology');
        $this->concept = $this->getFromData('concept');
        $this->conceptType = $this->getFromData('conceptType');
    }

    public function getEntityType(): CastorEntityType
    {
        return CastorEntityType::fromString($this->entityType);
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getEntityParent(): ?string
    {
        return $this->entityParent;
    }

    public function getOntology(): string
    {
        return $this->ontology;
    }

    public function getConcept(): string
    {
        return $this->concept;
    }

    public function getConceptType(): OntologyConceptType
    {
        return OntologyConceptType::fromString($this->conceptType);
    }
}

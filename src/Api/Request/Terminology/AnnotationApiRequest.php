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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $entityType;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $entityId;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $entityParent;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $ontology;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $concept;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $conceptType;

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

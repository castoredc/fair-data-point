<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Enum\StructureType;
use App\Entity\Terminology\Annotation;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Repository\CastorEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'castor_entity')]
#[ORM\Entity(repositoryClass: CastorEntityRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['step' => 'App\Entity\Castor\Structure\Step\Step', 'field' => 'App\Entity\Castor\Form\Field', 'field_option' => 'App\Entity\Castor\Form\FieldOption', 'field_option_group' => 'App\Entity\Castor\Form\FieldOptionGroup', 'structure_element' => 'App\Entity\Castor\Structure\StructureElement', 'report' => 'App\Entity\Castor\Structure\Report', 'survey' => 'App\Entity\Castor\Structure\Survey'])]
abstract class CastorEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 190)]
    protected string $id;

    #[ORM\JoinColumn(name: 'study_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: CastorStudy::class)]
    protected ?CastorStudy $study = null;

    #[ORM\Column(type: 'StructureType', name: 'structure_type', nullable: true)]
    protected ?StructureType $structureType = null;

    #[ORM\Column(type: 'string')]
    protected string $label;

    #[ORM\Column(type: 'string')]
    protected string $slug;

    /** @var Collection<string, Annotation> */
    #[ORM\OneToMany(targetEntity: Annotation::class, mappedBy: 'entity', cascade: ['persist'])]
    protected Collection $annotations;

    #[ORM\JoinColumn(name: 'parent', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['persist'])]
    private ?CastorEntity $parent = null;

    public function __construct(string $id, string $label, CastorStudy $study, ?StructureType $structureType)
    {
        $this->id = $id;
        $this->label = $label;
        $this->slug = $id;
        $this->study = $study;
        $this->structureType = $structureType;
        $this->annotations = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getStudy(): ?CastorStudy
    {
        return $this->study;
    }

    public function getStructureType(): ?StructureType
    {
        return $this->structureType;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /** @return Collection<string, Annotation> */
    public function getAnnotations(): Collection
    {
        return $this->annotations;
    }

    /** @return Annotation[] */
    public function getAnnotationsByOntology(Ontology $ontology): array
    {
        $return = [];

        foreach ($this->annotations as $annotation) {
            if ($annotation->getConcept()->getOntology() !== $ontology) {
                continue;
            }

            $return[] = $annotation;
        }

        return $return;
    }

    public function hasAnnotation(OntologyConcept $ontologyConcept): bool
    {
        foreach ($this->annotations as $annotation) {
            if ($annotation->getConcept() === $ontologyConcept) {
                return true;
            }
        }

        return false;
    }

    /** @param Collection<string, Annotation> $annotations */
    public function setAnnotations(Collection $annotations): void
    {
        $this->annotations = $annotations;
    }

    public function addAnnotation(Annotation $annotation): void
    {
        $this->annotations->add($annotation);
    }

    public function removeAnnotation(Annotation $annotation): void
    {
        $this->annotations->remove($annotation->getId());
    }

    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    public function getParent(): ?CastorEntity
    {
        return $this->parent;
    }

    public function setParent(?CastorEntity $parent): void
    {
        $this->parent = $parent;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    /** @return CastorEntity[]|null */
    public function getChildren(): ?array
    {
        return null;
    }

    public function getChild(string $id): ?CastorEntity
    {
        return null;
    }
}

<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Enum\StructureType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="castor_entity")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "step" = "App\Entity\Castor\Structure\Step\Step",
 *     "field" = "App\Entity\Castor\Form\Field",
 *     "field_option" = "App\Entity\Castor\Form\FieldOption",
 *     "field_option_group" = "App\Entity\Castor\Form\FieldOptionGroup",
 *     "structure_element" = "App\Entity\Castor\Structure\StructureElement",
 * })
 */
abstract class CastorEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\Study", inversedBy="metadata", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id")
     *
     * @var Study|null
     */
    protected $study;

    /**
     * @ORM\Column(type="StructureType", name="structure_type", nullable=true)
     *
     * @var StructureType|null
     */
    protected $structureType;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $slug;

    public function __construct(string $id, string $label, Study $study, ?StructureType $structureType)
    {
        $this->id = $id;
        $this->label = $label;
        $this->slug = $id;
        $this->study = $study;
        $this->structureType = $structureType;
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

    public function getStudy(): ?Study
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
}

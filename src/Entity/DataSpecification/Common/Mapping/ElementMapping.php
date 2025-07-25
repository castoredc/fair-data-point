<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Study;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_specification_mappings_element')]
#[ORM\Entity]
class ElementMapping extends Mapping
{
    #[ORM\JoinColumn(name: 'element', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Element::class)]
    private ?Element $element = null;

    /** @var Collection<CastorEntity> */
    #[ORM\ManyToMany(targetEntity: CastorEntity::class)]
    private Collection $entities;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $syntax = null;

    #[ORM\Column(type: 'boolean')]
    private bool $transformData = false;

    public function __construct(Study $study, ?Element $element, Version $version)
    {
        parent::__construct($study, $version);

        $this->entities = new ArrayCollection();
        $this->element = $element;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(?Element $element): void
    {
        $this->element = $element;
    }

    /** @return Collection<CastorEntity> */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    /** @param Collection<CastorEntity> $elements */
    public function setEntities(Collection $elements): void
    {
        $this->entities = $elements;
    }

    public function addEntity(CastorEntity $element): void
    {
        $this->entities->add($element);
    }

    public function removeEntity(CastorEntity $element): void
    {
        $this->entities->removeElement($element);
    }

    public function getSyntax(): ?string
    {
        return $this->syntax;
    }

    public function setSyntax(?string $syntax): void
    {
        $this->syntax = $syntax;
    }

    public function shouldTransformData(): bool
    {
        return $this->transformData;
    }

    public function setTransformData(bool $transformData): void
    {
        $this->transformData = $transformData;
    }
}

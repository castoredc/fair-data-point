<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataSpecification\Element;
use App\Entity\Data\DataSpecification\Version;
use App\Entity\Study;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class DataTransformationMapping extends Mapping
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Data\DataSpecification\Element")
     *
     * @var Collection<Element>
     */
    private Collection $elements;

    /** @ORM\Column(type="text") */
    private string $syntax;

    /**
     * @param ArrayCollection<Element> $elements
     */
    public function __construct(Study $study, ArrayCollection $elements, CastorEntity $entity, Version $version)
    {
        parent::__construct($study, $entity, $version);

        $this->elements = $elements;
    }

    /** @return Collection<Element> */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    /**
     * @param Collection<Element> $elements
     */
    public function setElements(Collection $elements): void
    {
        $this->elements = $elements;
    }

    public function addElement(Element $element): void
    {
        $this->elements->add($element);
    }

    public function removeElement(Element $element): void
    {
        $this->elements->removeElement($element);
    }

    public function getSyntax(): string
    {
        return $this->syntax;
    }

    public function setSyntax(string $syntax): void
    {
        $this->syntax = $syntax;
    }
}

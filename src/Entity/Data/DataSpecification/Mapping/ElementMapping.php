<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataSpecification\Element;
use App\Entity\Data\DataSpecification\Version;
use App\Entity\Study;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ElementMapping extends Mapping
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataSpecification\Element")
     * @ORM\JoinColumn(name="element", referencedColumnName="id")
     */
    private ?Element $element = null;

    public function __construct(Study $study, ?Element $element, CastorEntity $entity, Version $version)
    {
        parent::__construct($study, $entity, $version);

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
}

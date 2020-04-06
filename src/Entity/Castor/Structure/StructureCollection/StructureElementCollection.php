<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Structure\StructureElement;

abstract class StructureElementCollection
{
    /** @var StructureElement[] */
    protected $elements;

    public function add(StructureElement $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return StructureElement[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}

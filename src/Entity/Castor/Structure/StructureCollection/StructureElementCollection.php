<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Structure\StructureElement;
use function array_merge;

abstract class StructureElementCollection
{
    /** @var StructureElement[]|null */
    protected $elements;

    public function add(StructureElement $element): void
    {
        $this->elements[$element->getId()] = $element;
    }

    public function get(string $id): StructureElement
    {
        return $this->elements[$id];
    }

    /**
     * @return StructureElement[]|null
     */
    public function getElements(): ?array
    {
        return $this->elements;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->elements as $element) {
            foreach ($element->getSteps() as $step) {
                $fields = array_merge($fields, $step->getFields());
            }
        }

        return $fields;
    }
}

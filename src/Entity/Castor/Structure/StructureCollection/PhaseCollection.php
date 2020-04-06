<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Structure\Phase;
use function uasort;

class PhaseCollection extends StructureElementCollection
{
    public function orderSteps(): void
    {
        foreach ($this->elements as $phase) {
            /** @var Phase $phase */
            $phase->orderSteps();
        }
    }

    public function order(): void
    {
        uasort($this->elements, static function (Phase $a, Phase $b) {
            if ($a->getPosition() === $b->getPosition()) {
                return 0;
            }

            return $a->getPosition() < $b->getPosition() ? -1 : 1;
        });

        foreach ($this->elements as $element) {
            $element->orderFieldsInSteps();
        }
    }
}

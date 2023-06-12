<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Structure\Phase;
use App\Entity\Castor\Structure\StructureElement;
use function assert;
use function uasort;

class PhaseCollection extends StructureElementCollection
{
    public function order(): void
    {
        uasort($this->elements, static function (StructureElement $a, StructureElement $b) {
            assert($a instanceof Phase);
            assert($b instanceof Phase);

            if ($a->getPosition() === $b->getPosition()) {
                return 0;
            }

            return $a->getPosition() < $b->getPosition() ? -1 : 1;
        });

        foreach ($this->elements as $phase) {
            /** @var Phase $phase */
            $phase->orderSteps();
        }
    }
}

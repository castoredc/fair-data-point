<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Structure\Report;
use function strcasecmp;
use function uasort;

class ReportCollection extends StructureElementCollection
{
    public function order(): void
    {
        uasort($this->elements, static function (Report $a, Report $b): int {
            return strcasecmp($a->getName(), $b->getName());
        });

        foreach ($this->elements as $element) {
            $element->orderFieldsInSteps();
        }
    }
}

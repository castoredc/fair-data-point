<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Structure\StructureElement;
use function strcasecmp;
use function uasort;

class ReportCollection extends StructureElementCollection
{
    public function order(): void
    {
        uasort($this->elements, static function (StructureElement $a, StructureElement $b): int {
            return strcasecmp($a->getName(), $b->getName());
        });
    }
}

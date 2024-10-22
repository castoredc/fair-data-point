<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Enum\ResourceType;

class AssociatedItemCount
{
    /** @var array<string, int> */
    private array $counts = [];

    /** @return array<string, int> */
    public function getCounts(): array
    {
        return $this->counts;
    }

    public function addCount(ResourceType $type, int $count): void
    {
        $this->counts[$type->toString()] = $count;
    }
}

<?php
declare(strict_types=1);

namespace App\Command\FAIRData;

use App\Entity\FAIRData\AccessibleEntity;

class GetAssociatedItemCountCommand
{
    public function __construct(private AccessibleEntity $entity)
    {
    }

    public function getEntity(): AccessibleEntity
    {
        return $this->entity;
    }
}

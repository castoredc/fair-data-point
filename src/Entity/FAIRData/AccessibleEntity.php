<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

interface AccessibleEntity
{
    public function getRelativeUrl(): string;
}

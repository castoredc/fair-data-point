<?php
declare(strict_types=1);

namespace App\Api\Resource;

interface ApiResource
{
    /** @return array<mixed> */
    public function toArray(): array;
}

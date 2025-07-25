<?php
declare(strict_types=1);

namespace App\Exception;

interface ApiException
{
    /** @return array<mixed> */
    public function toArray(): array;
}

<?php
declare(strict_types=1);

namespace App\Entity\Encryption;

class SensitiveDataString
{
    public function __construct(private string $value)
    {
    }

    public function exposeAsString(): string
    {
        return $this->value;
    }

    /** @return mixed[] */
    public function __debugInfo(): array
    {
        return ['value' => 'hidden sensitive data'];
    }

    /** @return mixed[] */
    public function __sleep(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return 'hidden sensitive data';
    }
}

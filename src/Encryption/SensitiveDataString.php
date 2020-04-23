<?php
declare(strict_types=1);

namespace App\Encryption;

class SensitiveDataString
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function exposeAsString(): string
    {
        return $this->value;
    }
}

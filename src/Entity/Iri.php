<?php
declare(strict_types=1);

namespace App\Entity;

use function basename;

class Iri
{
    /** @var string */
    private $value;

    public function __construct(string $uri)
    {
        $this->value = $uri;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getBase(): string
    {
        return basename($this->value);
    }
}

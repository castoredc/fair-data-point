<?php
declare(strict_types=1);

namespace App\Entity;

use function array_key_exists;
use function basename;
use function parse_url;
use function strlen;
use function strrpos;
use function substr_replace;

class Iri
{
    private string $value;

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
        $parsed = parse_url($this->value);

        if ($parsed !== false && array_key_exists('fragment', $parsed)) {
            return $parsed['fragment'];
        }

        return basename($this->value);
    }

    public function getPrefix(): string
    {
        return substr_replace($this->value, '', strrpos($this->value, $this->getBase()), strlen($this->value));
    }
}

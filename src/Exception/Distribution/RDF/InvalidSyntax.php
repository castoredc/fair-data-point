<?php
declare(strict_types=1);

namespace App\Exception\Distribution\RDF;

use Exception;

class InvalidSyntax extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The syntax is invalid.'];
    }
}

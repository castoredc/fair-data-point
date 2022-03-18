<?php
declare(strict_types=1);

namespace App\Exception\Distribution\RDF;

use Exception;
use function sprintf;

class VariableNotSelected extends Exception
{
    private string $variableName;

    public function __construct(string $variableName)
    {
        parent::__construct();
        $this->variableName = $variableName;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => sprintf('The variable %s that is present in the syntax is not selected for transformation', $this->variableName)];
    }
}

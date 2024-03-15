<?php
declare(strict_types=1);

namespace App\Exception\DataSpecification\Common\Model;

use Exception;

class InvalidTripleType extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The triple is of an invalid type.'];
    }
}

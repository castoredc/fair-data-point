<?php
declare(strict_types=1);

namespace App\Exception\DataSpecification\Common\Model;

use Exception;

class InvalidNodeType extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'This type of node is not supported.'];
    }
}

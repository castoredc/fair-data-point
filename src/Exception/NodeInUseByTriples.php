<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class NodeInUseByTriples extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'This node is still in use by triples.'];
    }
}

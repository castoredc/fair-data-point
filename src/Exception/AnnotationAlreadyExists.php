<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class AnnotationAlreadyExists extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'The ontology concept is already attached to this entity.'];
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

class OntologyNotFound extends RenderableApiException
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'Ontology not found.'];
    }
}

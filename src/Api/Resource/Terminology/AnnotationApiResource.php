<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use App\Entity\Terminology\Annotation;

class AnnotationApiResource implements ApiResource
{
    public function __construct(private Annotation $annotation)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->annotation->getId(),
            'concept' => (new OntologyConceptApiResource($this->annotation->getConcept()))->toArray(),
        ];
    }
}

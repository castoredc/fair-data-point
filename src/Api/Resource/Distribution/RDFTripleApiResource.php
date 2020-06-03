<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\RDF\RDFTriple;

class RDFTripleApiResource implements ApiResource
{
    /** @var RDFTriple */
    private $triple;

    public function __construct(RDFTriple $triple)
    {
        $this->triple = $triple;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'subject' => (new RDFTripleElementApiResource($this->triple->getSubject()))->toArray(),
            'predicate' => (new RDFTripleElementApiResource($this->triple->getPredicate()))->toArray(),
            'object' => (new RDFTripleElementApiResource($this->triple->getObject()))->toArray(),
        ];
    }
}

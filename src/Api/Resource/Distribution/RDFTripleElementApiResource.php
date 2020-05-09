<?php

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\RDF\RDFTriple;
use App\Entity\Data\RDF\RDFTripleElement\RDFTripleElement;

class RDFTripleElementApiResource implements ApiResource
{
    /** @var RDFTripleElement */
    private $element;

    public function __construct(RDFTripleElement $element)
    {
        $this->element = $element;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->element->getLabel()
        ];
    }
}
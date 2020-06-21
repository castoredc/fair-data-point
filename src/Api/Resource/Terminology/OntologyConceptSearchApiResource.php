<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use Castor\BioPortal\Model\Concept;

class OntologyConceptSearchApiResource implements ApiResource
{
    /** @var Concept[] */
    private $concepts;

    /** @param Concept[] $concepts */
    public function __construct(array $concepts)
    {
        $this->concepts = $concepts;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->concepts as $concept) {
            $data[] = [
                'value' => $concept->getNotation(),
                'label' => $concept->getPrefLabel(),
            ];
        }

        return $data;
    }
}

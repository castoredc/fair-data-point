<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use Castor\BioPortal\Model\Concept;
use Castor\BioPortal\Model\Individual;

class OntologyConceptSearchApiResource implements ApiResource
{
    /** @var (Concept|Individual)[] */
    private array $concepts;

    /** @param (Concept|Individual)[] $concepts */
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
            if ($concept instanceof Concept) {
                $data[] = [
                    'value' => $concept->getNotation(),
                    'label' => $concept->getPrefLabel(),
                    'type' => 'concept',
                ];
            } else {
                $data[] = [
                    'value' => $concept->getId()->getBase(),
                    'label' => $concept->getLabel(),
                    'type' => 'individual',
                ];
            }
        }

        return $data;
    }
}

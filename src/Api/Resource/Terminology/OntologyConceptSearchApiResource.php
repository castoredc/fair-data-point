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
                    'type' => 'concept',
                    'url' => (string) $concept->getId(),
                    'code' => $concept->getNotation(),
                    'value' => $concept->getNotation(),
                    'label' => $concept->getPrefLabel(),
                ];
            } else {
                $data[] = [
                    'type' => 'individual',
                    'url' => (string) $concept->getId(),
                    'code' => $concept->getId()->getBase(),
                    'value' => $concept->getId()->getBase(),
                    'label' => $concept->getLabel(),
                ];
            }
        }

        return $data;
    }
}

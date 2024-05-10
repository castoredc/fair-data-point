<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\StudyMetadata;
use function in_array;
use function sort;

class StudyMetadataFilterApiResource implements ApiResource
{
    /** @param StudyMetadata[] $metadata */
    public function __construct(private array $metadata)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return $this->generateFilters();
    }

    /** @return array<mixed> */
    private function generateFilters(): array
    {
        $filters = [
            'country' => [],
            'studyType' => [],
            'methodType' => [],
        ];

        foreach ($this->metadata as $metadata) {
            foreach ($metadata->getCenters() as $center) {
                $filters = $this->addFilterItem($filters, 'country', $center->getOrganization()->getCountry()->getCode());
            }

            $filters = $this->addFilterItem($filters, 'methodType', $metadata->getMethodType()->toString());
            $filters = $this->addFilterItem($filters, 'studyType', $metadata->getType()->toString());
        }

        sort($filters['country']);
        sort($filters['studyType']);
        sort($filters['methodType']);

        return $filters;
    }

    /**
     * @param array<mixed> $filters
     *
     * @return array<mixed>
     */
    private function addFilterItem(array $filters, string $key, string $item): array
    {
        if (! in_array($item, $filters[$key], true)) {
            $filters[$key][] = $item;
        }

        return $filters;
    }
}

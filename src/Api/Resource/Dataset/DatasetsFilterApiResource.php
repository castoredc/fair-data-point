<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Department;
use function in_array;
use function sort;

class DatasetsFilterApiResource implements ApiResource
{
    /** @var Dataset[] */
    private $datasets;

    /**
     * @param Dataset[] $datasets
     */
    public function __construct(array $datasets)
    {
        $this->datasets = $datasets;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->generateFilters();
    }

    /**
     * @return array<mixed>
     */
    private function generateFilters(): array
    {
        $filters = [
            'country' => [],
            'studyType' => [],
            'methodType' => [],
        ];

        foreach ($this->datasets as $dataset) {
            $metadata = $dataset->getStudy()->getLatestMetadata();

            foreach ($metadata->getCenters() as $center) {
                if (! ($center instanceof Department)) {
                    continue;
                }

                $filters = $this->addFilterItem($filters, 'country', $center->getOrganization()->getCountry()->getCode());
            }

            if ($metadata->getMethodType() !== null) {
                $filters = $this->addFilterItem($filters, 'methodType', $metadata->getMethodType()->toString());
            }

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

<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Dataset;

class AdminPaginatedDatasetsApiResource implements ApiResource
{
    /** @var Dataset[] */
    private $datasets;

    /** @var int */
    private $perPage;

    /** @var int */
    private $page;

    /** @var int */
    private $pages;

    /**
     * @param Dataset[] $datasets
     */
    public function __construct(array $datasets, int $perPage, int $page, int $pages)
    {
        $this->datasets = $datasets;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->pages = $pages;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'perPage' => $this->perPage,
            'page' => $this->page,
            'pages' => $this->pages,
            'datasets' => [],
        ];

        foreach ($this->datasets as $dataset) {
            $data['datasets'][] = (new AdminDatasetApiResource($dataset))->toArray();
        }

        return $data;
    }
}

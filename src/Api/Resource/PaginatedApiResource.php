<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\PaginatedResultCollection;

class PaginatedApiResource implements ApiResource
{
    /** @var string */
    private $apiResourceType;

    /** @var PaginatedResultCollection<mixed> */
    private $results;

    /** @var bool */
    private $isAdmin;

    public function __construct(string $apiResourceType, PaginatedResultCollection $results, bool $isAdmin = false)
    {
        $this->apiResourceType = $apiResourceType;
        $this->results = $results;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $results = [];

        foreach ($this->results as $result) {
            $resource = new $this->apiResourceType($result);

            if ($resource instanceof RoleBasedApiResource) {
                $resource->setAdmin($this->isAdmin);
            }

            $results[] = $resource->toArray();
        }

        return [
            'results' => $results,
            'currentPage' => $this->results->getCurrentPage(),
            'start' => $this->results->getStart(),
            'perPage' => $this->results->getPerPage(),
            'totalResults' => $this->results->getTotalResults(),
            'totalPages' => $this->results->getTotalPages(),
        ];
    }
}

<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\PaginatedResultCollection;
use App\Service\UriHelper;

class PaginatedApiResource implements ApiResource
{
    private string $apiResourceType;

    /** @var PaginatedResultCollection<mixed> */
    private PaginatedResultCollection $results;

    private bool $isAdmin;

    private ?UriHelper $uriHelper;

    public function __construct(string $apiResourceType, PaginatedResultCollection $results, bool $isAdmin = false, ?UriHelper $uriHelper = null)
    {
        $this->apiResourceType = $apiResourceType;
        $this->results = $results;
        $this->isAdmin = $isAdmin;
        $this->uriHelper = $uriHelper;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $results = [];

        foreach ($this->results as $result) {
            if ($this->uriHelper !== null) {
                $resource = new $this->apiResourceType($result, $this->uriHelper);
            } else {
                $resource = new $this->apiResourceType($result);
            }

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

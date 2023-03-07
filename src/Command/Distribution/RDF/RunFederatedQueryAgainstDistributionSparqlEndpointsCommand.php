<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

class RunFederatedQueryAgainstDistributionSparqlEndpointsCommand
{
    /** @var string[] */
    private array $distributionIds;
    private string $query;

    /** @param string[] $distributionIds */
    public function __construct(array $distributionIds, string $query)
    {
        $this->distributionIds = $distributionIds;
        $this->query = $query;
    }

    /** @return string[] */
    public function getDistributionIds(): array
    {
        return $this->distributionIds;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}

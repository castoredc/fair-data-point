<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

class RunFederatedQueryAgainstDistributionSparqlEndpointsCommand
{
    /** @param string[] $distributionIds */
    public function __construct(private array $distributionIds, private string $query)
    {
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

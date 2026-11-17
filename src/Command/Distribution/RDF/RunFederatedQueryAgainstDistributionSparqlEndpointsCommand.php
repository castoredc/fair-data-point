<?php
declare(strict_types=1);

namespace App\Command\Distribution\RDF;

/**
 * @deprecated Stardog support was a proof of concept and is no longer in use.
 *             This Stardog-only code path is kept for reference, is not covered by tests,
 *             and should not be built upon.
 */
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

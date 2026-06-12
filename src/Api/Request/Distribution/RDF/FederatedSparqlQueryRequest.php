<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution\RDF;

use App\Api\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use function explode;

/**
 * @deprecated Stardog support was a proof of concept and is no longer in use.
 *             This Stardog-only code path is kept for reference, is not covered by tests,
 *             and should not be built upon.
 */
class FederatedSparqlQueryRequest extends ApiRequest
{
    public function __construct(Request $request)
    {
        $data = $request->request->all();
        $this->query = $request->query;

        parent::__construct($data, $request->query);
    }

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $sparqlQuery;

    /** @var string[] */
    #[Assert\Type('array')]
    private array $distributionIds;

    protected function parse(): void
    {
        $this->sparqlQuery = $this->getFromData('query') ?? $this->getFromQuery('query');

        $distributionIds = $this->getFromData('distributions') ?? $this->getFromQuery('distributions');
        $this->distributionIds = explode(';', $distributionIds);
    }

    public function getSparqlQuery(): string
    {
        return $this->sparqlQuery;
    }

    /** @return string[] */
    public function getDistributionIds(): array
    {
        return $this->distributionIds;
    }
}

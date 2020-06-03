<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\RDF\RDFDistribution;

class AddRDFDistributionPrefixCommand
{
    /** @var RDFDistribution */
    private $distribution;

    /** @var string */
    private $prefix;

    /** @var string */
    private $uri;

    public function __construct(string $prefix, string $uri, RDFDistribution $distribution)
    {
        $this->prefix = $prefix;
        $this->uri = $uri;
        $this->distribution = $distribution;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }
}

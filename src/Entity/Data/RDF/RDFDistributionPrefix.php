<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rdf_prefix")
 */
class RDFDistributionPrefix
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $prefix;

    /**
     * @ORM\Column(type="iri", nullable=false)
     *
     * @var Iri
     */
    private $uri;

    /**
     * @ORM\ManyToOne(targetEntity="RDFDistribution", inversedBy="prefixes",cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="distribution", nullable=false)
     *
     * @var RDFDistribution
     */
    private $distribution;

    public function __construct(string $prefix, Iri $uri)
    {
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getUri(): Iri
    {
        return $this->uri;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setUri(Iri $uri): void
    {
        $this->uri = $uri;
    }

    public function setDistribution(RDFDistribution $distribution): void
    {
        $this->distribution = $distribution;
    }
}
